<?php

namespace LaraFiler;

use Cache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LaraFiler\LaraFiler;
use LaraFiler\Response\LaraFilerResponse;
use LaraFiler\LaraFilerThumbnailMaker;
use LaraFiler\Models\LarafmDocument;

class LaraFilerUploader
{

    protected $supportedMimetypes = [];
    protected $laraFiler = null;
    protected $file = null;

    protected $lastErrorMessage = null;
    protected $lastErrorStatusCode = null;
    protected $eloquent = null;


    public function __construct(LaraFiler $laraFiler, UploadedFile $file)
    {
        $this->laraFiler = $laraFiler;
        $this->file = $file;
    }

    public function process($groupName, $thumbs = false)
    {
        $file = $this->file;
        $laraFiler = $this->laraFiler;

        if (!in_array($file->getMimeType(), $laraFiler->getSupportedMimeTypes())) {
            return new LaraFilerResponse('Mimetype is not supported', 400);
        }
        if (in_array($file->getClientOriginalExtension(), $laraFiler->getForbiddenExtensions())) {
            return new LaraFilerResponse('Extension is not allowed', 400);
        }
        if ($file->getSize() > $laraFiler->getMaxUploadSize()) {
            return new LaraFilerResponse("Max upload size exceeded.", 400);
        }

        $singlePath = $laraFiler->getCustomUploadPath() ?? date('/y/m/d/');
        $path = $singlePath;
        $slug = Str::random(32);

        $size = $file->getSize();
        $totalSize = $size + LarafmDocument::where('created_by', Auth::id())->sum('size');

        if ($totalSize > $laraFiler->getMaxUserCapacity()) {
            return new LaraFilerResponse("Maximum capacity exceeded. Please remove some files and try again.", 403);
        }

        $doc = new LarafmDocument([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'slug' => $slug,
            'size' => $file->getSize(),
            'type' => $file->getType(),
            'mimetype' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'created_by' => auth()->user() ? auth()->user()->id : 0,
            'group_name' => $groupName
        ]);

        try {
            $thumbMaker = new LaraFilerThumbnailMaker($laraFiler->getThumbs());
            $thumbMaker->create($file, $singlePath, $slug);
            $doc->thumbs = implode(',', $thumbMaker->getCreatedThumbnails());
        } catch (\Exception $e) {
            return new LaraFilerResponse("Some error occurred while saving thumbnails", 500);
        }


        try {
            $doc->save();
        } catch (\Exception $e) {
            return new LaraFilerResponse("Database insertion failed: " . $e->getMessage(), 500);
        }


        $savedDoc = LarafmDocument::where('slug', $slug)->first();
        $file->move(\LaraFiler\Facades\LaraFiler::getDirPath($savedDoc), $savedDoc->slug . '.' . $savedDoc->extension);

        if ($laraFiler->isCached()) {
            Cache::forever('larafm.document', $savedDoc);
        }


        $this->eloquent = $savedDoc;

        return $this;
    }

    public function eloquent()
    {
        return $this->eloquent;
    }
}
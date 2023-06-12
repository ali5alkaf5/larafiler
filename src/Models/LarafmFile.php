<?php

namespace LaraFiler\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use LaraFiler\Facades\LaraFiler;
use LaraFiler\LaraFilerThumbnailMaker;
use LaraFiler\Response\LaraFilerResponse;
use LaraFiler\Models\LarafmDocument;

class LarafmFile
{

    protected $document = null;
    protected $path = null;
    protected $mimetype = null;
    public function __construct(LarafmDocument $document, $path, $mimetype)
    {
        $this->document = $document;
        $this->path = $path;
        $this->mimetype = $mimetype;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMimetype()
    {
        return $this->mimetype;
    }

    public function getFilename()
    {
        return $this->document->filename ?? null;
    }

    public function eloquent()
    {
        return $this->document;
    }

    private function response($contentDisposition = 'attachment')
    {
        if (!file_exists($this->path)) {
            return new LaraFilerResponse('File doesn\'t exist', 404);
        }
        $file = file_get_contents($this->path);

        $response = Response::make($file);
        $response->header('Content-Type', $this->getMimetype());
        $response->header('Content-Disposition', $contentDisposition . ';filename="' . $this->getFilename() . '"');
        $response->header('Cache-Control', 'public');
        $response->header('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($this->path)) . ' GMT');
        $response->header('Expires', gmdate('D, d M Y H:i:s', strtotime('+1 year')) . ' GMT');
        return $response;
    }

    public function download()
    {
        return $this->response('attachment');
    }

    public function inline()
    {
        return $this->response('inline');
    }

    public static function delete($doc)
    {
        if (!$doc) {
            return new LaraFilerResponse('Document not found', 404);
        }
        if ($doc instanceof LarafmDocument) {
            $document = $doc;
        } else {
            $document = LarafmDocument::where('slug', $doc)->first();
        }
        if (!$doc) {
            return new LaraFilerResponse('File not found', 404);
        }
        if (LaraFiler::isCached()) {
            Cache::forget('document.slug' . $document->slug);
        }
        $path = LaraFiler::getRelativePath($document, null);

        try {
            if ($document->thumbs) {
                LaraFilerThumbnailMaker::delete($document);
            }
            File::delete($path);
            $document->delete();
        } catch (\Exception $e) {
            return new LaraFilerResponse($e->getMessage(), 500);
        }

        return new LaraFilerResponse('Document has already been removed', 200);
    }
}
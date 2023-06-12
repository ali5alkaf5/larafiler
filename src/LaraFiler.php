<?php

namespace LaraFiler;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use LaraFiler\Response\LaraFilerResponse;
use LaraFiler\LaraFilerUploader;
use LaraFiler\Models\LarafmDocument;
use LaraFiler\Models\LarafmFile;

class LaraFiler
{

    protected $ffmpegPath = null;
    protected $ffprobePath = null;
    protected $cache = false;
    protected $storagePath = '/';
    protected $supportedMimeTypes = [];
    protected $forbiddenExtensions = [];
    protected $iconThumb = 'icon';
    protected $thumbs = [];
    protected $maxUploadSize = -1;
    protected $maxUserCapacity = -1;
    protected $customUploadPath = null;

    public function __construct()
    {
        $this->storagePath = rtrim(config('larafm.base_path', storage_path('app')), '/');
        $this->ffmpegPath = config('larafm.ffmpeg', null);
        $this->ffprobePath = config('larafm.ffprobe', null);
        $this->supportedMimeTypes = config('larafm.supported_mimetypes', []);
        $this->forbiddenExtensions = config('larafm.forbidden_extensions', []);
        $this->iconThumb = config('larafm.icon_thumb', 'icon');
        $this->thumbs = config('larafm.thumbs', [
            $this->iconThumb => 100,
        ]);
        $this->maxUploadSize = config('larafm.max_upload_size', 2 << 10);
        $this->maxUserCapacity = config('larafm.max_user_capacity', 2 << 10);
        $this->cache = config('larafm.cache', false);
    }

    public function setCustomUploadPath($path)
    {
        $this->customUploadPath = $path;
    }

    public function getCustomUploadPath()
    {
        return $this->customUploadPath;
    }

    public function upload(UploadedFile $file, $groupName = 'main', $thumbs = true)
    {
        $uploader = new LaraFilerUploader($this, $file);
        return $uploader->process($groupName, $thumbs);
    }

    public function find($slug, $size)
    {
        if ($this->isCached()) {
            $document = Cache::rememberForever('document.' . $slug, function () use ($slug) {
                return LarafmDocument::where('slug', $slug)->first();
            });
        } else {
            $document = LarafmDocument::where('slug', $slug)->first();
        }

        if (!$document) {
            return new LaraFilerResponse('Document not found', 404);
        }

        $basePath = $this->getStoragePath();

        $path = $basePath . $document->path . $document->slug . '.' . $document->extension;

        if (!$size) {
            $path = $basePath . $document->path . $document->slug . '.' . $document->extension;
        } else {
            $tmp = $path;
            if (in_array($size, explode(',', $document->thumbs))) {
                $path = str_replace($basePath . DIRECTORY_SEPARATOR, $basePath . DIRECTORY_SEPARATOR . $size . '/', $path);
            }
            if ($size === $this->getIconThumb()) {
                $path = $basePath . $document->path . $document->slug . '.jpg';
                $path = str_replace($basePath . DIRECTORY_SEPARATOR, $basePath . DIRECTORY_SEPARATOR . $size . '/', $path);
            }
            if (!file_exists($path)) {
                $path = $tmp;
            }
        }

        if (!file_exists($path)) {
            return new LaraFilerResponse('File doesn\'t exist', 404);
        }

        return new LarafmFile($document, $path, $size === $this->getIconThumb() ? 'image/jpeg' : $document->mimetype);
    }

    public function download($slug, $size = null)
    {
        $laraFiler = $this->find($slug, $size);
        if ($laraFiler instanceof LarafmFile) {
            return $laraFiler->download();
        }

        return $laraFiler;
    }

    public function inline($slug, $size = null)
    {
        $laraFiler = $this->find($slug, $size);
        if ($laraFiler instanceof LarafmFile) {
            return $laraFiler->inline();
        }

        return $laraFiler;
    }

    public function getFullPath($document)
    {
        return $this->getStoragePath() . $document->path . $document->slug . '.' . $document->extension;
    }

    public function getDirPath($document)
    {
        return $this->getStoragePath() . $document->path;
    }

    public function getRelativePath($document, $thumb)
    {
        $basePath = $this->storagePath;
        $path = $basePath . DIRECTORY_SEPARATOR . $thumb . $document->path . $document->slug . '.' . $document->extension;

        if ($thumb) {
            if ($thumb === $this->getIconThumb()) {
                $newPath = $basePath . $document->path . $document->slug . '.jpg';
                $filePath = str_replace($basePath, $basePath . $thumb . '/', $newPath);
                return $filePath;
            }
            $filePath = str_replace($basePath, $basePath . $thumb . '/', $path);
            return $filePath;
        }
        return $this->getFullPath($document);
    }

    public function delete($slug)
    {
        return LarafmFile::delete($slug);
    }

    public function getStoragePath()
    {
        return $this->storagePath;
    }
    public function getSupportedMimeTypes()
    {
        return $this->supportedMimeTypes;
    }

    public function getForbiddenExtensions()
    {
        return $this->forbiddenExtensions;
    }

    public function getMaxUploadSize()
    {
        return $this->maxUploadSize;
    }

    public function getMaxUserCapacity()
    {
        return $this->maxUserCapacity;
    }

    public function getIconThumb()
    {
        return $this->iconThumb;
    }
    public function getThumbs()
    {
        return $this->thumbs;
    }

    public function isCached()
    {
        return $this->cache;
    }
}
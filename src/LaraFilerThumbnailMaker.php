<?php

namespace LaraFiler;

use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\File;
use Intervention\Image\Image;
use LaraFiler\Facades\LaraFiler;
use Pawlox\VideoThumbnail\VideoThumbnail;

class LaraFilerThumbnailMaker
{

    protected $thumbConfig = null;
    protected $readyThumbnails = [];
    public function __construct($thumbConfig = [])
    {
        $this->thumbConfig = $thumbConfig;
    }

    private function saveAsImage($file, $path, $slug, $thumb = 'icon', $width, $height)
    {
        $interventionImage = new Image();
        $image = $interventionImage->make($file)->resize($width, $height, function ($constraint) use ($width, $height) {
            if (!$width || !$height) {
                $constraint->aspectRatio();
            }
            $constraint->upsize();
        });
        if ($thumb === config('larafm.icon_thumb')) {
            $image = $image->encode('jpg', 10);
            $image->save($path . $slug . ".jpg");
        } else {
            $image->save($path . $slug . "." . $file->getClientOriginalExtension());
        }
        $image->destroy();
    }

    private function saveAsVideo($file, $path, $slug, $thumb = 'icon', $width, $height)
    {
        $videoThumbnail = new VideoThumbnail();

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('video-thumbnail.binaries.ffmpeg'),
            'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);
        $dimensions = $ffmpeg->open($file->getRealPath())->getStreams()->videos()->first()->getDimensions();
        $videoThumbnail->createThumbnail(
            $file->getRealPath(),
            $path,
            $slug . ".jpg",
            0,
            $width,
            $height ?? $width * $dimensions->getHeight() / $dimensions->getWidth()
        );
    }


    public function create($file, $customPath, $slug)
    {
        $_thumbs = [];
        if (!(str_starts_with($file->getMimeType(), 'image') || str_starts_with($file->getMimeType(), 'video'))) {
            return [];
        }
        foreach ($this->thumbConfig as $thumb => $arr) {
            $width = gettype($arr) === 'integer' ? $arr : ($arr[0] ?? null);
            $height = gettype($arr) === 'integer' ? null : ($arr[1] ?? null);

            $basePath = rtrim(config('larafm.base_path', storage_path('app')), DIRECTORY_SEPARATOR);

            $thumbPath = $basePath . DIRECTORY_SEPARATOR . $thumb . DIRECTORY_SEPARATOR . $customPath;
            if (!file_exists($thumbPath)) {
                mkdir($thumbPath, 0755, true);
            }

            try {
                if (str_starts_with($file->getMimeType(), 'image')) {
                    $this->saveAsImage($file, $thumbPath, $slug, $thumb, $width, $height);
                } else if (str_starts_with($file->getMimeType(), 'video')) {
                    $this->saveAsVideo($file, $thumbPath, $slug, $thumb, $width, $height);
                }
                $_thumbs[] = $thumb;

            } catch (\Exception $e) {
                if (config('larafm.exception_handling', true)) {
                    throw $e;
                }
            }
        }
        $this->readyThumbnails = $_thumbs;
        return $this;
    }

    public static function delete(LarafmDocument $document)
    {
        if (!$document) {
            return false;
        }
        $thumbs = explode(",", $document->thumbs);

        foreach ($thumbs as $name) {
            $path = LaraFiler::getThumbPath($document, $name);
            if ($path && file_exists($path)) {
                File::delete($path);
            }
        }
        return true;
    }

    public function getCreatedThumbnails()
    {
        return $this->readyThumbnails;
    }

}
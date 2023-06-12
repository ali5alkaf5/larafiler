<?php

return [

    /**
     * Base Path
     */

    'base_path' => storage_path('app/larafm'),

    /**
     * FFMPEG Location
     */
    'ffmpeg' => env('LARAFM_FFMPEG', '/usr/bin/ffmpeg'),
    'ffprobe' => env('LARAFM_FFPROBE', '/usr/bin/ffprobe'),

    /** 
     * List of Allowed Mimetypes.
     */

    'supported_mimetypes' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/jpg',
        'image/webp',
        'audio/mpeg',
        'audio/ogg',
        'audio/mp4',
        'video/mpeg',
        'video/ogg',
        'video/mp4',
        'application/vnd.oasis.opendocument.presentation',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/pdf',
        'text/plain',
    ],

    /**
     * Forbidden extensions that are not allowed to be executed on any device.
     * Some javascript files can have mimetype of text/plain, describe it here to
     * prevent malicious files from being uploaded.
     * 
     */
    'forbidden_extensions' => [
        'js',
        'exe',
        'apk'
    ],

    /**
     * Base icon key/folder for thumbnail
     */
    'icon_thumb' => env('LARAFM_FILE_ICON', 'icon'),

    /**
     * Thumbnails created for image preview and sizes
     */
    'thumbs' => [
        env('LARAFM_FILE_ICON', 'icon') => 100,
        // This is required, don't change anything
        'tiny' => 150,
        'small' => 300,
        'medium' => 480,
        'high' => 720,
    ],

    /**
     * Maximum Upload Size in bytes
     */

    'max_upload_size' => 10485760,

    /**
     * Maximum User's File Capacity in bytes
     */

    'max_user_capacity' => 104857600,

    /**
     * Exception Handling
     */
    'exception_handling' => false,

    /**
     * Cache storing
     */
    'cache' => true,
];
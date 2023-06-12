# LaraFiler

LaraFiler is a well-structured file manager package for Laravel. It provides a convenient way to manage and handle files within your Laravel application. With LaraFiler, you can easily upload, download, delete, and organize files.

## Installation

You can install LaraFiler via Composer by running the following command:

```bash
composer require larafiler/larafiler
```

LaraFiler will be automatically discovered and registered in your Laravel application.

## Configuration
After installing LaraFiler, you can publish its configuration file using the following command:

```bash
php artisan vendor:publish --provider="LaraFiler\Providers\LaraFilerServiceProvider"
```

This will publish the `larafm.php` configuration file to your application's `config` directory. You can customize the settings in this file according to your needs.

## Usage
### Uploading Files
To upload a file using LaraFiler, you can use the `LaraFiler` facade's `upload` method. Here's an example:

```php
use LaraFiler\Facades\LaraFiler;

$file = $request->file('file');
$path = LaraFiler::upload($file); // Must be instance of Illuminate\Http\UploadedFile
```

The `upload` method accepts an instance of `Illuminate\Http\UploadedFile` as the first parameter. It will store the file in the appropriate location and return the file path.

### Downloading or viewing Files
You can download or view a file using LaraFiler by providing the file path to the `download` or `inline` method:

```php
use LaraFiler\Facades\LaraFiler;
use LaraFiler\Models\LarafmDocument;

$slug = 'FV8gQzAa4DkUHaM6NaWBKgYCdRWomry4';
$document = LarafmDocument::where('slug', $slug)->first();
return LaraFiler::download($document);
// or
return LaraFiler::inline($document);
// or
return LaraFiler::download($slug);
// or
return LaraFiler::inline($slug);

```

The `download` || `inline` method will return response with the document or slug as parameters.

### Deleting Files
To delete a file, you can use the `delete` method:

```php
use LaraFiler\Facades\LaraFiler;
use LaraFiler\Models\LarafmDocument;

$slug = 'FV8gQzAa4DkUHaM6NaWBKgYCdRWomry4';
$document = LarafmDocument::where('slug', $slug)->first();
return LaraFiler::delete($document);

// or
return LaraFiler::delete($slug);
```

The `delete` method will remove the file and thumbnails created from the storage and database.

## API Reference
### LaraFiler Facade
The LaraFiler facade provides the following methods:

- `upload(UploadedFile $file): LaraFilerResponse`: Uploads the given file and returns the file path.
- `download(LarafmDocument|string $doc): LaraFilerResponse`: Downloads the file with the given path for download.
- `inline(LarafmDocument|string $doc): LaraFilerResponse`: Streams the file with the given path for download.
- `delete(LarafmDocument|string $doc): bool`: Deletes the file with the given path. Returns true on success.

## License
LaraFiler is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing
Thank you for considering contributing to LaraFiler! You can contribute by opening issues, submitting pull requests, or providing suggestions and feedback.

## Support
If you encounter any issues or have any questions or suggestions, please feel free to [open an issue](https://github.com/ali5alkaf5/larafiler/issues) on GitHub.

## Credits
LaraFiler is developed and maintained by [Ali Akbar](https://github.com/ali5alkaf5)

## Acknowledgements
We would like to express our gratitude to the Laravel community for their continuous support and inspiration.

## Changelog
Detailed changes for each release will be documented in the [release notes](https://github.com/ali5alkaf5/larafiler/releases).
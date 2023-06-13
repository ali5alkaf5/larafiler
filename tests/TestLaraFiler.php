<?php

use LaraFiler\Factory;
use LaraFiler\Facades\LaraFiler;
use LaraFiler\Providers\LaraFilerServiceProvider;
use LaraFiler\LaraFilerUploader;
use Illuminate\Http\UploadedFile;
use Orchestra\Testbench\TestCase;

class TestLaraFiler extends TestCase
{

    protected $successFile;
    protected $errorFile;

    protected function getPackageProviders($app)
    {
        $factory = new Factory();
        $factory->runMigrations();
        return [
            LaraFilerServiceProvider::class,
        ];
    }
    public function test1FileUpload()
    {
        $file = UploadedFile::fake()->create('test_file.text', 100, 'text/plain'); // Creates a 100KB dummy file
        // Call your file upload method with the created file
        $result = LaraFiler::upload($file);

        $this->assertInstanceOf(LaraFilerUploader::class, $result);
        if ($result instanceof LaraFilerUploader) {
            $this->successFile = $result->eloquent();

            $path = LaraFiler::getFullPath($this->successFile);
            $this->assertFileExists($path);
        }

        $this->test2FileDelete();
    }

    /**
     * @test
     * @depends test1FileUpload
     */
    protected function test2FileDelete()
    {
        $response = LaraFiler::delete($this->successFile);
        $this->assertEquals(200, $response->getStatus());
    }
}
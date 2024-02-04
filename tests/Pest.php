<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Alireza\LaravelFileExplorer\tests\LaravelFileExplorerTestCase;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\Storage;

uses(LaravelFileExplorerTestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createFakeFiles(int $count = 1, string $dirName = "ios"): array
{
    $files = [];
    for ($i = 0; $i < $count; $i++) {
        $path = $dirName . "/fake_file_" . $i . ".txt";
        $files[] = $path;
        Storage::disk("tests")->put($path, "");
    }

    return $files;
}

function createFakeDirs(int $count = 1, string $dirName = "ios"): array
{
    $dirs = [];
    for ($i = 0; $i < $count; $i++) {
        $dir["name"] = "fake_dir_" . $i;
        if ($dirName) {
            $dir["path"] = $dirName . "/fake_dir_" . $i;
        }
        else {
            $dir["path"] = "fake_dir_" . $i;
        }
        $dirs[] = $dir;
        Storage::disk("tests")->makeDirectory($dir["path"]);
    }

    return $dirs;
}

function createFakeImages(int $count = 1, string $dirName = "ios"): array
{
    $fileFactory = new FileFactory();
    $images = [];
    for ($i = 0; $i < $count; $i++) {
        $imageName = "fake_image_" . $i . ".png";
        $image = $fileFactory->image($imageName);
        Storage::disk("tests")->putFileAs($dirName, $image, $imageName);
        $images[] = $imageName;
    }

    return $images;
}

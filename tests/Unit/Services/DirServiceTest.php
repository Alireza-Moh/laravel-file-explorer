<?php

use Alireza\LaravelFileExplorer\Events\DirCreated;
use Alireza\LaravelFileExplorer\Events\ItemDeleted;
use Alireza\LaravelFileExplorer\Services\DirService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

function assertItemValues(array $item, array $expectedValues): void
{
    foreach ($expectedValues as $key => $value) {
        test()->expect($item[$key])->toBe($value);
    }
}

test('should retrieve directory items', function () {
    createFakeDirs();
    createFakeFiles();
    createFakeImages();
    $dirService = new DirService();

    $dirItems = $dirService->getDirItems("tests", "ios");

    $this->assertCount(3, $dirItems);
    assertItemValues($dirItems[0], [
        'diskName' => 'tests',
        'name' => 'fake_file_0.txt',
        'path' => 'ios/fake_file_0.txt',
        'type' => 'file',
        'extension' => 'txt',
        'url' => '/storage/ios/fake_file_0.txt',
    ]);
    assertItemValues($dirItems[1], [
        'diskName' => 'tests',
        'name' => 'fake_image_0.png',
        'path' => 'ios/fake_image_0.png',
        'type' => 'file',
        'extension' => 'png',
        'url' => '/storage/ios/fake_image_0.png',
    ]);
    assertItemValues($dirItems[2], [
        'diskName' => 'tests',
        'name' => 'fake_dir_0',
        'path' => 'ios/fake_dir_0',
        'type' => 'dir',
        'size' => '-',
        'extension' => null,
        'url' => '/storage/ios/fake_dir_0',
    ]);
});

test('should retrieve disk directories', function () {
    createFakeDirs(dirName: "");
    $dirService = new DirService();

    $dirs = $dirService->getDiskDirsForTree("tests");

    expect($dirs)->toBeArray()
        ->and($dirs)->toEqual(
            [
                [
                    "diskName" => "tests",
                    "dirName" => "",
                    "name" => "fake_dir_0",
                    "path" => "fake_dir_0",
                    "type" => "dir",
                    "subDir" => []
                ]
            ]
        )
        ->and($dirs)->toHaveLength(1);
});

test('should retrieve disk files', function () {
    createFakeFiles(2, "");
    $dirService = new DirService();

    $items = $dirService->getDiskItems("tests");

    expect($items)->toBeArray()
        ->and($items[0])->toBeArray()
        ->and($items[0])->toHaveKeys([
            "diskName",
            "name",
            "path",
            "type",
            "extension",
            "url",
            "size",
            "lastModified"
        ])
        ->and($items[0]['diskName'])->toBe('tests')
        ->and($items[0]['name'])->toBe('fake_file_0.txt')
        ->and($items[0]['path'])->toBe('fake_file_0.txt')
        ->and($items[0]['type'])->toBe('file')
        ->and($items[0]['extension'])->toBe('txt')
        ->and($items[0]['url'])->toBe('/storage/fake_file_0.txt')
        ->and($items[0]['size'])->toBe("-")
        ->and($items[0]['lastModified'])->toBeString()
        ->and($items[1])->toBeArray()
        ->and($items[1])->toHaveKeys([
            "diskName",
            "name",
            "path",
            "type",
            "extension",
            "url",
            "size",
            "lastModified"
        ])
        ->and($items[1]['diskName'])->toBe('tests')
        ->and($items[1]['name'])->toBe('fake_file_1.txt')
        ->and($items[1]['path'])->toBe('fake_file_1.txt')
        ->and($items[1]['type'])->toBe('file')
        ->and($items[1]['extension'])->toBe('txt')
        ->and($items[1]['url'])->toBe('/storage/fake_file_1.txt')
        ->and($items[1]['size'])->toBe("-")
        ->and($items[1]['lastModified'])->toBeString();
});

test('should find directory by name', function () {
    $dirs = createFakeDirs();
    $dirService = new DirService();

    $foundedDir = $dirService->findDirectoryByName("tests", $dirs[0]["name"]);

    expect($foundedDir)->toBeArray()
        ->and($foundedDir)->toMatchArray([
            "diskName" => "tests",
              "name" => "fake_dir_0",
              "path" => "ios/fake_dir_0",
              "type" => "dir",
              "subDir" => []
            ]);
});

test('should not find directory by name', function () {
    $dirService = new DirService();

    $foundedDir = $dirService->findDirectoryByName("tests", "notExistingDir");

    expect($foundedDir)->toBeNull();
});

test('should delete specified directory', function () {
    Event::fake();
    $dirService = new DirService();
    $dir = createFakeDirs();

    $result = $dirService->delete("tests", [
        'items' => [
            [
                "name" => $dir[0]["name"],
                'path' => $dir[0]["path"]
            ],
        ]
    ]);

    Storage::disk("tests")->assertMissing($dir[0]["path"]);
    expect($result)->toBeArray()
        ->and($result)->toMatchArray([
           "result" => [
               "status" => "success",
               "message" => "Directory deleted successfully"
           ]
        ]);
    Event::assertDispatched(ItemDeleted::class);
});

test('should create a directory', function () {
    Event::fake();
    $dirService = new DirService();

    $result = $dirService->create("tests", [
        "destination" => "ios",
        "path" => "ios/zjztj"
    ]);

    Storage::disk("tests")->assertExists("ios/zjztj");
    expect($result)->toBeArray()
        ->and($result)->toMatchArray(
            [
                "result" => [
                    "status" => "success",
                    "message" => "Directory created successfully",
                    "items" => [
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "zjztj",
                            "path" => "ios/zjztj",
                            "type" => "dir",
                            "size" => "-",
                            "lastModified" => "-",
                            "extension" => null,
                            "url" => "/storage/ios/zjztj",
                        ]
                    ],
                    "dirs" => [
                        [
                            "diskName" => "tests",
                            "dirName" => "",
                            "name" => "ios",
                            "path" => "ios",
                            "type" => "dir",
                            "subDir" => [
                                [
                                    "diskName" => "tests",
                                    "dirName" => "ios",
                                    "name" => "zjztj",
                                    "path" => "ios/zjztj",
                                    "type" => "dir",
                                    "subDir" => []
                                ]
                            ]

                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(DirCreated::class);
});

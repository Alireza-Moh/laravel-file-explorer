<?php

use Alireza\LaravelFileExplorer\Events\FileCreated;
use Alireza\LaravelFileExplorer\Events\ItemDeleted;
use Alireza\LaravelFileExplorer\Events\ItemRenamed;
use Alireza\LaravelFileExplorer\Events\ItemUploaded;
use Alireza\LaravelFileExplorer\Services\ItemService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Event::fake();
});

test('should rename a given file', function () {
    $file = createFakeFiles();
    $fileService = new ItemService();

    $result = $fileService->rename(
        "tests",
        $file[0],
        [
            "newName" => "newNamdde.txt",
            "oldPath" => $file[0],
            "newPath" => "ios/newNamdde.txt"
        ]
    );

    expect($result)->toBeArray()
        ->and($result)->toMatchArray([
            "result" => [
                "status" => "success",
                "message" => "Item renamed successfully"

            ]
        ]);
    Event::assertDispatched(ItemRenamed::class);
});

test('should delete a given file', function () {
    $fileService = new ItemService();
    Storage::disk("tests")->put("ios/test.txt", "");

    $result = $fileService->delete("tests", [
        'items' => [
            [
                "name" => "test.txt",
                'path' => "ios/test.txt"
            ],
        ]
    ]);

    Storage::disk("tests")->assertMissing("ios/test.txt");
    expect($result)->toBeArray()
        ->and($result)->toMatchArray([
            "result" => [
                "status" => "success",
                "message" => "File deleted successfully"
            ]
        ]);
    Event::assertDispatched(ItemDeleted::class);
});

test('should upload a single item', function () {
    $fileService = new ItemService();

    $fileService->upload(
        "tests",
        [
            "ifFileExist" => 0,
            "destination" => "ios",
            "items" => [
                UploadedFile::fake()->image('photo1.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertExists('ios/photo1.jpg');
    Event::assertDispatched(ItemUploaded::class);
});

test('should upload multiple items', function () {
    $fileService = new ItemService();

    $result = $fileService->upload(
        "tests",
        [
            "ifFileExist" => 0,
            "destination" => "ios",
            "items" => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
                UploadedFile::fake()->image('photo3.jpg'),
                UploadedFile::fake()->image('photo4.jpg')
            ]
        ]
    );

    foreach ($result['result']['items'] as &$item) {
        unset($item['size'], $item['lastModified']);
    }
    Storage::disk('tests')->assertExists(['ios/photo1.jpg', "ios/photo2.jpg", "ios/photo3.jpg", "ios/photo4.jpg"]);
    expect($result)->toBeArray()
        ->and($result)->toMatchArray(
            [
                "result" => [
                    "status" => "success",
                    "message" => "Items uploaded successfully",
                    "items" => [
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "photo1.jpg",
                            "path" => "ios/photo1.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo1.jpg",
                        ],
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "photo2.jpg",
                            "path" => "ios/photo2.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo2.jpg"
                        ],
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "photo3.jpg",
                            "path" => "ios/photo3.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo3.jpg"
                        ],
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "photo4.jpg",
                            "path" => "ios/photo4.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo4.jpg"
                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(ItemUploaded::class);
});

test('should create a file', function () {
    $fileService = new ItemService();

    $result = $fileService->create("tests", [
        "destination" => "ios",
        "path" => "ios/zjztj.txt"
    ]);

    foreach ($result['result']['items'] as &$item) {
        unset($item['size'], $item['lastModified']);
    }

    Storage::disk("tests")->assertExists("ios/zjztj.txt");
    expect($result)->toBeArray()
        ->and($result)->toMatchArray(
            [
                "result" => [
                    "status" => "success",
                    "message" => "File created successfully",
                    "items" => [
                        [
                            "diskName" => "tests",
                            "dirName" => "ios",
                            "name" => "zjztj.txt",
                            "path" => "ios/zjztj.txt",
                            "type" => "file",
                            "extension" => "txt",
                            "url" => "/storage/ios/zjztj.txt",
                        ]
                    ],
                    "dirs" => [
                        [
                            "diskName" => "tests",
                            "dirName" => "",
                            "name" => "ios",
                            "path" => "ios",
                            "type" => "dir",
                            "subDir" => []

                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(FileCreated::class);
});

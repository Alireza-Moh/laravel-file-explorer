<?php

use Alireza\LaravelFileExplorer\Services\DirService;
use Alireza\LaravelFileExplorer\Services\FileService;
use Illuminate\Http\UploadedFile;

test('should rename a given file', function () {
    $file = createFakeFiles();
    $fileService = new FileService();

    $result = $fileService->rename(
        "tests",
        $file[0],
        [
            "oldPath" => $file[0],
            "newPath" => "ios/newNamdde.txt"
        ]
    );

    expect($result)->toBeArray()
        ->and($result)->toMatchArray([
            "result" => [
                "status" => "success",
                "message" => "File renamed successfully"

            ]
        ]);
});

test('should delete a given file', function () {
    $fileService = new FileService();
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
});

test('should upload a given file', function () {
    $fileService = new FileService();

    $fileService->upload(
        "tests",
        [
            "ifFileExist" => 0,
            "destination" => "ios",
            "files" => [
                UploadedFile::fake()->image('photo1.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertExists('ios/photo1.jpg');
});

test('should upload multiple files', function () {
    $fileService = new FileService();

    $result = $fileService->upload(
        "tests",
        [
            "ifFileExist" => 0,
            "destination" => "ios",
            "files" => [
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
                    "message" => "File uploaded successfully",
                    "items" => [
                        [
                            "diskName" => "tests",
                            "name" => "photo1.jpg",
                            "path" => "ios/photo1.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo1.jpg",
                        ],
                        [
                            "diskName" => "tests",
                            "name" => "photo2.jpg",
                            "path" => "ios/photo2.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo2.jpg"
                        ],
                        [
                            "diskName" => "tests",
                            "name" => "photo3.jpg",
                            "path" => "ios/photo3.jpg",
                            "type" => "file",
                            "extension" => "jpg",
                            "url" => "/storage/ios/photo3.jpg"
                        ],
                        [
                            "diskName" => "tests",
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
});

test('should create a file', function () {
    $fileService = new FileService();

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
                            "name" => "ios",
                            "path" => "ios",
                            "type" => "dir",
                            "subDir" => []

                        ]
                    ]
                ]
            ]
        );
});

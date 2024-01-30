<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should create file and return success response with all file inside the directory', function () {
    $response = $this->postJson(
        route(
            "fx.file-create",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "destination" => "ios",
            "path" => "ios/config.txt"
        ]
    );

    Storage::disk('tests')->assertExists('ios/config.txt');
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message",
            "result.items",
            "result.dirs"
        ])
        ->where("result.status", "success")
        ->where("result.message", "File created successfully")
        ->has('result.items')
        ->has('result.dirs')
        ->has('result.items.0', fn(AssertableJson $json) =>
            $json->where("name", "config.txt")
                ->where('name', 'config.txt')
                ->where('path', 'ios/config.txt')
                ->where('type', 'file')
                ->where('size', 0)
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/config.txt')
                ->etc()
        )
    );
});

test('should throw an error when path is missing in the form data for creating a file', function () {
    $response = $this->postJson(
        route(
            "fx.file-create",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "type" => "file",
            "dirPath" => "ios"
            //"path" => "ios/config.txt"
        ]
    );

    Storage::disk('tests')->assertMissing('ios/config.txt');
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            "message",
            "errors"
        ])->where("message", "Invalid data sent")
        ->has('errors')
        ->has('errors.path')
        ->where('errors.path.0', 'The path field is required.')
        ->etc()
    );
});

test('should upload file or files and return success response with all file inside the directory', function () {
    $response = $this->postJson(
        route(
            "fx.file-upload",
            ["diskName" => "tests"]
        ),
        [
            "ifFileExist" => 0,
            "destination" => "ios",
            "files" => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertExists(['ios/photo1.jpg', 'ios/photo2.jpg']);
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message",
            "result.items"
        ])
        ->where("result.status", "success")
        ->where("result.message", "File uploaded successfully")
        ->has('result.items')
        ->has('result.items.0', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'photo1.jpg')
            ->where('path', 'ios/photo1.jpg')
            ->where('type', 'file')
            ->where('extension', 'jpg')
            ->where('url', '/storage/ios/photo1.jpg')
            ->etc()
        )
        ->has('result.items.1', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'photo2.jpg')
            ->where('path', 'ios/photo2.jpg')
            ->where('type', 'file')
            ->where('extension', 'jpg')
            ->where('url', '/storage/ios/photo2.jpg')
            ->etc()
        )
    );
});

test('should throw an error when ifFileExist is missing in the form while uploading files', function () {
    $response = $this->postJson(
        route(
            "fx.file-upload",
            ["diskName" => "tests"]
        ),
        [
            //"ifFileExist" => 0,
            "destination" => "ios",
            "files" => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertMissing(['ios/photo1.jpg', 'ios/photo2.jpg']);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            "message",
            "errors"
        ])
        ->where("message", "Invalid data sent")
        ->where(
            'errors',
            [
                "photo1.jpg" => ["Choose an action"],
                "photo2.jpg" => ["Choose an action"]
            ]
        )
    );
});

test('should download a single image', function () {
    $image = createFakeImages();

    $response = $this->postJson(
        route(
            "fx.file-download",
            ["diskName" => "tests"]
        ),
        [
            "files" => [
                [
                    "name" => $image[0],
                    "path" => "ios/" . $image[0],
                    "type" => "file",
                ]
            ]
        ]
    );

    $response->assertDownload();
});

test('should download multiple files as a ZIP folder', function () {
    $images = createFakeImages(2);

    $response = $this->postJson(
        route(
            "fx.file-download",
            ["diskName" => "tests"]
        ),
        [
            "files" => [
                [
                    "name" => $images[0],
                    "path" => "ios/" . $images[0],
                    "type" => "file"
                ],
                [
                    "name" => $images[1],
                    "path" => "ios/" . $images[1],
                    "type" => "file"
                ]
            ]
        ]
    );

    $response->assertDownload();
    $response->assertHeader('Content-Type', 'application/zip');
    $response->assertHeader('Content-Disposition', 'attachment; filename=tests_files.zip');
});

test('should throw validation error when trying to download a directory ', function () {
    $dir = createFakeDirs();

    $response = $this->postJson(
        route(
            "fx.file-download",
            ["diskName" => "tests"]
        ),
        [
            "files" => [
                [
                    "name" => $dir[0]["name"],
                    "path" => $dir[0]["path"],
                    "type" => "dir",
                ]
            ]
        ]
    );

    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('message')
        ->where('message', 'Invalid data sent')
        ->has('errors')
        ->has('errors.' . $dir[0]["name"])
        ->where('errors.' . $dir[0]["name"]. '.0', 'Invalid file type')
    );
});

test('should rename a file', function () {
    $images = createFakeImages();
    $response = $this->putJson(
        route(
            "fx.file-rename",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "newPath" => "ios/newName.png",
            "oldPath" => "ios/" . $images[0],
        ]
    );

    Storage::disk('tests')->assertExists('ios/newName.png');
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message"
        ])
        ->where("result.status", "success")
        ->where("result.message", "File renamed successfully")
    );
});

test('should throw an error when something is missing in form for renaming a file', function () {
    $response = $this->putJson(
        route(
            "fx.file-rename",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "newPath" => "ios/newName.png",
            //"oldPath" => "ios/oldName.png",
        ]
    );

    Storage::disk('tests')->assertMissing('ios/newName.png');
    $response->assertJson(fn (AssertableJson $json) =>
    $json->hasAll([
        "message",
        "errors"
    ])
        ->where("message", "Invalid data sent")
        ->has('errors')
        ->has('errors.oldPath')
        ->where('errors.oldPath.0', 'The old path field is required.')
    );
});

test('should delete one file', function () {
    $images = createFakeImages();

    $response = $this->deleteJson(
        route(
            "fx.file-delete",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
               [
                   "name" => $images[0],
                   "path" => "ios/" . $images[0],
                   "type" => "file"
               ]
            ]
        ]
    );

    Storage::disk("tests")->assertMissing("ios/" . $images[0]);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('result')
            ->hasAll([
                "result.status",
                "result.message"
            ])
            ->where("result.status", "success")
            ->where("result.message", "File deleted successfully")
    );
});

test('should delete multiple files', function () {
    $images = createFakeImages(10);

    $imagesToDelete = [];
    foreach ($images as $image) {
        $imagesToDelete[] = [
            "name" => $image,
            "path" => "ios/" . $image,
        ];
    }
    $response = $this->deleteJson(
        route(
            "fx.file-delete",
            ["diskName" => "tests"]
        ),
        [
            "items" => $imagesToDelete
        ]
    );

    $paths = array_column($imagesToDelete, 'path');
    Storage::disk("tests")->assertMissing($paths);
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message"
        ])
        ->where("result.status", "success")
        ->where("result.message", "File deleted successfully")
    );
});

test('should throw an error when something is missing in form for deleting a file', function () {
    $images = createFakeImages();

    $response = $this->deleteJson(
        route(
            "fx.file-delete",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
                [
                    "name" => $images[0]
                    //"path" => "ios/" . $images[0]
                ]
            ]
        ]
    );

    Storage::disk("tests")->assertExists("ios/" . $images[0]);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            "message",
            "errors"
        ])
        ->where("message", "Invalid data sent")
    );
});

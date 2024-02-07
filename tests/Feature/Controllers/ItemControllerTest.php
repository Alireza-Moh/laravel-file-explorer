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
                ->where('size', "-")
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

test('should upload item or items and return success response with all items inside the directory', function () {
    $response = $this->postJson(
        route(
            "fx.items-upload",
            ["diskName" => "tests"]
        ),
        [
            "ifItemExist" => 0,
            "destination" => "ios",
            "items" => [
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
        ->where("result.message", "Items uploaded successfully")
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

test('should throw an error when ifItemExist is missing in the form while uploading items', function () {
    $response = $this->postJson(
        route(
            "fx.items-upload",
            ["diskName" => "tests"]
        ),
        [
            //"ifItemExist" => 0,
            "destination" => "ios",
            "items" => [
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
                "ifItemExist" => ["Choose an action"],
            ]
        )
    );
});

test('should throw an error when items have wrong extension while uploading items', function () {
    $response = $this->postJson(
        route(
            "fx.items-upload",
            ["diskName" => "tests"]
        ),
        [
            "ifItemExist" => 0,
            "destination" => "ios",
            "items" => [
                UploadedFile::fake()->create('doc1.pdf'),
                UploadedFile::fake()->create('doc2.pdf')
            ]
        ]
    );

    Storage::disk('tests')->assertMissing(['ios/doc1.pdf', 'ios/doc2.pdf']);
    $response->assertJson(fn (AssertableJson $json) =>
    $json->hasAll([
        "message",
        "errors"
    ])
        ->where("message", "Invalid data sent")
        ->where(
            'errors',
            [
                "doc1.pdf" => ["File extension not allowed"],
                "doc2.pdf" => ["File extension not allowed"]
            ]
        )
    );
});

test('should download a single item', function () {
    $image = createFakeImages();

    $response = $this->postJson(
        route(
            "fx.items-download",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
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

test('should download multiple items as a ZIP folder', function () {
    $images = createFakeImages(2);

    $response = $this->postJson(
        route(
            "fx.items-download",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
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

test('should rename a file', function () {
    $images = createFakeImages();
    $response = $this->putJson(
        route(
            "fx.item-rename",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "oldName" => $images[0],
            "newName" => "newName.png",
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
        ->where("result.message", "Item renamed successfully")
    );
});

test('should throw an error when something is missing in form for renaming a file', function () {
    $response = $this->putJson(
        route(
            "fx.item-rename",
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
            "fx.items-delete",
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
            "fx.items-delete",
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
            "fx.items-delete",
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

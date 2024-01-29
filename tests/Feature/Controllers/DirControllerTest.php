<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should create directory and return success response with all file inside the directory', function () {
    $response = $this->postJson(
        route(
            "fx.dir-create",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "destination" => "ios",
            "path" => "ios/configDir"
        ]
    );

    Storage::disk("tests")->assertExists("ios/configDir");
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message",
            "result.items",
            "result.dirs"
        ])
        ->where("result.status", "success")
        ->where("result.message", "Directory created successfully")
        ->has('result.items')
        ->has('result.dirs')
        ->has('result.items.0', fn(AssertableJson $json) =>
            $json->where("diskName", "tests")
                ->where('name', 'configDir')
                ->where('path', 'ios/configDir')
                ->where('type', 'dir')
                ->where('size', '-')
                ->where('lastModified', '-')
                ->where('extension', null)
                ->where('url', '/storage/ios/configDir')
            )
            ->has('result.dirs.0', fn(AssertableJson $json) =>
                $json->where("diskName", "tests")
                    ->where('name', 'ios')
                    ->where('path', 'ios')
                    ->where('type', 'dir')
                    ->has('subDir')
                )
        ->where('result.dirs.0.subDir.0', [
            "diskName" => "tests",
            "name" => "configDir",
            "path" => "ios/configDir",
            "type" => "dir",
            "subDir" => []
        ])
    );
});

test('should throw an error when form data is missing', function () {
    $response = $this->postJson(
        route("fx.dir-create",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "type" => "dir",
            "dirPath" => "ios"
            //"path" => "ios/configDir"
        ]
    );

    Storage::disk("tests")->assertMissing("ios/configDir");
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

test('should get all items from a specified directory', function () {
    createFakeFiles(5);
    createFakeDirs();

    $response = $this->postJson(
        route(
            "fx.load-dir-items",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            "path" => "ios"
        ]
    );

    $response->assertJson(fn (AssertableJson $json) =>
    $json->hasAll([
        "dirName",
        "items",
        "selectedDirPath"
    ])
        ->where("dirName", "ios")
        ->has('items')
        ->has('items.0', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_file_0.txt')
            ->where('path', 'ios/fake_file_0.txt')
            ->where('type', 'file')
            ->where('size', 0)
            ->where('extension', 'txt')
            ->where('url', '/storage/ios/fake_file_0.txt')
            ->etc()
        )
        ->has('items.1', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_file_1.txt')
            ->where('path', 'ios/fake_file_1.txt')
            ->where('type', 'file')
            ->where('size', 0)
            ->where('extension', 'txt')
            ->where('url', '/storage/ios/fake_file_1.txt')
            ->etc()
        )
        ->has('items.2', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_file_2.txt')
            ->where('path', 'ios/fake_file_2.txt')
            ->where('type', 'file')
            ->where('size', 0)
            ->where('extension', 'txt')
            ->where('url', '/storage/ios/fake_file_2.txt')
            ->etc()
        )
        ->has('items.3', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_file_3.txt')
            ->where('path', 'ios/fake_file_3.txt')
            ->where('type', 'file')
            ->where('size', 0)
            ->where('extension', 'txt')
            ->where('url', '/storage/ios/fake_file_3.txt')
            ->etc()
        )
        ->has('items.4', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_file_4.txt')
            ->where('path', 'ios/fake_file_4.txt')
            ->where('type', 'file')
            ->where('size', 0)
            ->where('extension', 'txt')
            ->where('url', '/storage/ios/fake_file_4.txt')
            ->etc()
        )
        ->has('items.5', fn(AssertableJson $json) =>
        $json->where("diskName", "tests")
            ->where('name', 'fake_dir_0')
            ->where('path', 'ios/fake_dir_0')
            ->where('type', 'dir')
            ->where('size', '-')
            ->where('extension', null)
            ->where('url', '/storage/ios/fake_dir_0')
            ->etc()
        )
        ->where("selectedDirPath", "ios")
    );
});

test('should throw an error when directory path is missing in form', function () {
    createFakeFiles(5);
    createFakeDirs();

    $response = $this->postJson(
        route(
            "fx.load-dir-items",
            ["diskName" => "tests", "dirName" => "ios"]
        ),
        [
            //"path" => "ios"
        ]
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            "message",
            "errors"
        ])
        ->where("message", "The path field is required.")
        ->has('errors')
        ->has('errors.path')
        ->where('errors.path.0', 'The path field is required.')
    );
});

test('should rename a file', function () {
    Storage::disk("tests")->makeDirectory("ios/oldName");
    $response = $this->putJson(
        route(
            "fx.dir-rename",
            ["diskName" => "tests", "dirName" => "oldName"]
        ),
        [
            "newPath" => "ios/newName",
            "oldPath" => "ios/oldName",
        ]
    );

    Storage::disk('tests')->assertExists("ios/newName");
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message"
        ])
        ->where("result.status", "success")
        ->where("result.message", "Directory renamed successfully")
    );
});

test('should throw an error when something is missing in form for renaming a file', function () {
    $response = $this->putJson(
        route(
            "fx.dir-rename",
            ["diskName" => "tests", "dirName" => "oldName"]
        ),
        [
            "newPath" => "ios/newName",
            //"oldPath" => "ios/oldName",
        ]
    );

    Storage::disk('tests')->assertMissing('ios/newName');
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

test('should delete one directory', function () {
    $dirs = createFakeDirs();
    $response = $this->deleteJson(
        route(
            "fx.dir-delete",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
                [
                    "name" => $dirs[0]["name"],
                    "path" => "ios/" . $dirs[0]["path"],
                    "type" => "dir"
                ]
            ]
        ]
    );

    Storage::disk("tests")->assertMissing("ios/" . $dirs[0]["path"]);
    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.message"
        ])
        ->where("result.status", "success")
        ->where("result.message", "Directory deleted successfully")
    );
});

test('should delete multiple files', function () {
    $dirs = createFakeDirs(10);
    $imagesToDelete = [];
    foreach ($dirs as $dir) {
        $imagesToDelete[] = [
            "name" => $dir["name"],
            "path" => $dir["path"],
            "type" => "dir"
        ];
    }
    $response = $this->deleteJson(
        route(
            "fx.dir-delete",
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
        ->where("result.message", "Directory deleted successfully")
    );
});

test('should throw an error when something is missing in form for deleting a directory', function () {
    $dirs = createFakeDirs();
    $response = $this->deleteJson(
        route(
            "fx.dir-delete",
            ["diskName" => "tests"]
        ),
        [
            "items" => [
                [
                    "name" => $dirs[0]["name"],
                    //"path" => $dirs[0]["path"]
                ]
            ]
        ]
    );


    Storage::disk("tests")->assertExists($dirs[0]["path"]);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            "message",
            "errors"
        ])
        ->where("message", "Invalid data sent")
    );
});

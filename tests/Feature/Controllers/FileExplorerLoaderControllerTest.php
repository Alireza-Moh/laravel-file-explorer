<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should load file explorer initial data', function () {
    createFakeImages(2);
    createFakeFiles(2);
    createFakeDirs(2);
    $response = $this->getJson(route("fx.init-file-explorer"));

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('result')
            ->hasAll([
                "result.status",
                "result.data.disks",
                "result.data",
                "result.data.dirsForSelectedDisk",
                "result.data.selectedDisk",
                "result.data.selectedDirPath",
                "result.data.selectedDirItems"
            ])
            ->where("result.status", "success")
            ->where("result.data.disks", ["tests", "web", "images"])
            ->where("result.data.selectedDisk", "tests")
            ->where("result.data.selectedDir", "ios")
            ->where("result.data.selectedDirPath", "ios")
            ->has('result.data.dirsForSelectedDisk')
            ->where("result.data.dirsForSelectedDisk.diskName", "tests")
            ->has('result.data.dirsForSelectedDisk.dirs.0', fn(AssertableJson $json) =>
                $json->where("diskName", "tests")
                    ->where('name', 'ios')
                    ->where('path', 'ios')
                    ->where('type', 'dir')
                    ->has('subDir')
                    ->where('subDir.0', [
                        "diskName" => "tests",
                        "name" => "fake_dir_1",
                        "path" => "ios/fake_dir_1",
                        "type" => "dir",
                        "subDir" => []
                    ])
                    ->where('subDir.1', [
                        "diskName" => "tests",
                        "name" => "fake_dir_0",
                        "path" => "ios/fake_dir_0",
                        "type" => "dir",
                        "subDir" => []
                    ])
                )
            ->has('result.data.selectedDirItems')
            ->has("result.data.selectedDirItems.0", fn(AssertableJson $json) =>
                $json->where("diskName", "tests")
                    ->where("name", "fake_file_0.txt")
                    ->where("path", "ios/fake_file_0.txt")
                    ->where("type", "file")
                    ->where("extension", "txt")
                    ->where("url", "/storage/ios/fake_file_0.txt")
                    ->etc()
                )
            ->has("result.data.selectedDirItems.1", fn(AssertableJson $json) =>
                $json->where("diskName", "tests")
                    ->where("name", "fake_file_1.txt")
                    ->where("path", "ios/fake_file_1.txt")
                    ->where("type", "file")
                    ->where("extension", "txt")
                    ->where("url", "/storage/ios/fake_file_1.txt")
                    ->etc()
                )
        );
});

test('should load File Explorer initial data with no items and no directories on selected disk "tests"', function () {
    Storage::fake('tests');

    $response = $this->getJson(route("fx.init-file-explorer"));

    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('result')
        ->hasAll([
            "result.status",
            "result.data.disks",
            "result.data",
            "result.data.dirsForSelectedDisk",
            "result.data.selectedDisk",
            "result.data.selectedDirPath",
            "result.data.selectedDirItems"
        ])
        ->where("result.status", "success")
        ->where("result.data.disks", ["tests", "web", "images"])
        ->has('result.data.dirsForSelectedDisk')
        ->where("result.data.dirsForSelectedDisk.dirs", [])
        ->where("result.data.dirsForSelectedDisk.diskName", "tests")
        ->where("result.data.selectedDisk", "tests")
        ->where("result.data.selectedDir", "ios")
        ->where("result.data.selectedDirPath", "")
        ->where("result.data.selectedDirItems", [])
    );
});

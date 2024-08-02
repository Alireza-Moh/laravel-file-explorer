<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should throw an error when disk does not exist', function () {
    $response = $this->postJson(
        route(
            'fx.dir-create',
            ['diskName' => 'aa', 'dirName' => 'ios']
        ),
        [
            'destination' => 'ios',
            'path' => 'ios/configDir'
        ]
    );

    $response->assertStatus(422);
    $response->assertJson(fn (AssertableJson $json) =>
    $json->hasAll([
        'status',
        'message',
        'result'
    ])
        ->where('status', 'failed')
        ->where('message', 'Disk aa does not exist')
    );
});

test('should create directory and return success response with all file inside the directory', function () {
    $response = $this->postJson(
        route(
            'fx.dir-create',
            ['diskName' => 'tests', 'dirName' => 'ios']
        ),
        [
            'destination' => 'ios',
            'path' => 'ios/configDir'
        ]
    );

    Storage::disk('tests')->assertExists('ios/configDir');
    $response->assertJson(fn (AssertableJson $json) =>
    $json->where('status', 'success')
        ->where('message', 'Directory created successfully')
        ->hasAll([
            'result.items',
            'result.dirs'
        ])
        ->has('result.items')
        ->has('result.items.0', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('parent', 'ios')
                ->where('name', 'configDir')
                ->where('path', 'ios/configDir')
                ->where('type', 'dir')
                ->etc()
            )
        ->has('result.dirs')
        ->has('result.dirs.0', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('parent', null)
                ->where('name', 'ios')
                ->where('path', 'ios')
                ->where('type', 'dir')
                ->etc()
            )
        ->etc()
    );
});

test('should throw an error when form data is missing', function () {
    $response = $this->postJson(
        route('fx.dir-create',
            ['diskName' => 'tests', 'dirName' => 'ios']
        ),
        [
            'type' => 'dir',
            'dirPath' => 'ios'
            //'path' => 'ios/configDir'
        ]
    );

    Storage::disk('tests')->assertMissing('ios/configDir');

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->has('errors')
        ->has('errors.path')
        ->where('errors.path.0', 'Directory path is required')
        ->etc()
    );
});

test('should get all items from a specified directory', function () {
    createFakeFiles(5);
    createFakeDirs();

    $response = $this->getJson(
        route(
            'fx.load-dir-items',
            [
                'diskName' => 'tests',
                'dirName' => 'ios'
            ]
        )
        . '?path=ios',
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', '')
            ->has('result.items')
            ->has('result.items.0', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_dir_0')
                ->where('path', 'ios/fake_dir_0')
                ->where('type', 'dir')
                ->where('extension', null)
                ->where('url', '/storage/ios/fake_dir_0')
                ->etc()
            )
            ->has('result.items.1', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_file_0.txt')
                ->where('path', 'ios/fake_file_0.txt')
                ->where('type', 'file')
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/fake_file_0.txt')
                ->etc()
            )
            ->has('result.items.2', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_file_1.txt')
                ->where('path', 'ios/fake_file_1.txt')
                ->where('type', 'file')
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/fake_file_1.txt')
                ->etc()
            )
            ->has('result.items.3', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_file_2.txt')
                ->where('path', 'ios/fake_file_2.txt')
                ->where('type', 'file')
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/fake_file_2.txt')
                ->etc()
            )
            ->has('result.items.4', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_file_3.txt')
                ->where('path', 'ios/fake_file_3.txt')
                ->where('type', 'file')
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/fake_file_3.txt')
                ->etc()
            )
            ->has('result.items.5', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'fake_file_4.txt')
                ->where('path', 'ios/fake_file_4.txt')
                ->where('type', 'file')
                ->where('extension', 'txt')
                ->where('url', '/storage/ios/fake_file_4.txt')
                ->etc()
            )
            ->where('result.selectedDirPath', 'ios')
            ->where('result.dirName', 'ios')
    );
});

test('should throw an error when directory path is missing in form', function () {
    createFakeFiles(5);
    createFakeDirs();

    $response = $this->getJson(
        route(
            'fx.load-dir-items',
            [
                'diskName' => 'tests',
                'dirName' => 'ios'
            ]
        )
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->has('errors')
        ->where('errors.path.0', 'File or Directory path is missing')
    );
});

<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should load file explorer initial data', function () {
    createFakeImages(2);
    createFakeFiles(2);
    createFakeDirs(2);
    $response = $this->getJson(route('fx.init-explorer'));

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('status')
            ->where('status', 'success')
            ->has('message')
            ->has('result')
            ->where('result.disks', ['tests', 'web', 'images'])
            ->where('result.selectedDisk', 'tests')
            ->where('result.selectedDir', 'ios')
            ->where('result.selectedDirPath', 'ios')
            ->has('result.selectedDirItems')
            ->has('result.selectedDirItems.0', fn (AssertableJson $json) =>
                $json->where('diskName', 'tests')
                    ->where('parent', 'ios')
                    ->where('name', 'fake_dir_1')
                    ->where('type', 'dir')
                    ->etc()
            )
            ->has('result.dirsForSelectedDisk.0', fn (AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('parent', null)
                ->where('name', 'ios')
                ->where('type', 'dir')
                ->has('subDir', 2)
                ->has('subDir.0', fn (AssertableJson $json) =>
                    $json->where('diskName', 'tests')
                        ->where('parent', 'ios')
                        ->where('name', 'fake_dir_1')
                        ->where('type', 'dir')
                        ->etc()
                )
                ->etc()
            )
    );
});

test('should load File Explorer initial data with no items and no directories on selected disk tests', function () {
    Storage::fake('tests');

    $response = $this->getJson(route('fx.init-explorer'));

    $response->assertJson(fn (AssertableJson $json) =>
    $json->has('status')
        ->where('status', 'success')
        ->has('message')
        ->has('result')
        ->where('result.disks', ['tests', 'web', 'images'])
        ->where('result.selectedDisk', 'tests')
        ->where('result.selectedDir', 'ios')
        ->where('result.selectedDirPath', null)
        ->has('result.selectedDirItems', 0)
        ->has('result.dirsForSelectedDisk', 0)
    );
});

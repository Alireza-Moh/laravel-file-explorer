<?php

use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use AlirezaMoh\LaravelFileExplorer\Supports\Item;

beforeEach(function () {
    Storage::fake('local');
    $this->diskManager = new DiskManager('local');
});

test('should set up directories correctly', function () {
    Storage::disk('local')->makeDirectory('exampleDir');

    $diskManager = new DiskManager('local');
    $directories = $diskManager->directories;

    expect($directories)->toHaveCount(1)
        ->and($directories->first()->name)->toBe('exampleDir')
        ->and($directories->first())->toBeInstanceOf(Item::class);

});

test('should set up files correctly', function () {
    Storage::disk('local')->put('exampleFile.txt', 'content');

    $diskManager = new DiskManager('local');
    $files = $diskManager->diskFiles;

    expect($files)->toHaveCount(1)
        ->and($files->first()->name)->toBe('exampleFile.txt');
});

test('should find a directory by name', function () {
    Storage::disk('local')->makeDirectory('exampleDir');

    $diskManager = new DiskManager('local');
    $directory = $diskManager->findDirectoryByName('exampleDir');

    expect($directory)->not->toBeNull()
        ->and($directory->name)->toBe('exampleDir');
});

test('should get items by directory name', function () {
    Storage::disk('local')->makeDirectory('exampleDir');
    Storage::disk('local')->put('exampleDir/exampleFile.txt', 'content');

    $diskManager = new DiskManager('local');
    $items = $diskManager->getItemsByParentName('exampleDir', 'exampleDir');

    expect($items)->toHaveCount(1)
        ->and($items[0]->name)->toBe('exampleFile.txt');
});

test('should create an item', function () {
    Storage::disk('local')->makeDirectory('exampleDir');

    $diskManager = new DiskManager('local');
    $item = $diskManager->createItem('dir', 'exampleDir', '');

    expect($item)->not->toBeNull()
        ->and($item->name)->toBe('exampleDir');
});

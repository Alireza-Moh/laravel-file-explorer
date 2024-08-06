<?php

use AlirezaMoh\LaravelFileExplorer\Events\FileCreated;
use AlirezaMoh\LaravelFileExplorer\Events\ItemDeleted;
use AlirezaMoh\LaravelFileExplorer\Events\ItemRenamed;
use AlirezaMoh\LaravelFileExplorer\Events\ItemUploaded;
use AlirezaMoh\LaravelFileExplorer\Services\ItemService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Event::fake();
});

test('should rename a given file', function () {
    $file = createFakeFiles();
    $itemService = new ItemService();

    $response = $itemService->rename(
        'tests',
        [
            'oldName' => 'fake_file_0.txt',
            'newName' => 'newNamdde.txt',
            'oldPath' => $file[0],
            'newPath' => 'ios/newNamdde.txt',
            'type' => 'file',
            'parent' => 'ios'
        ]
    );

    $parsedResponse = $response->getData(true);
    expect($parsedResponse)->toBeArray()
        ->and($parsedResponse['result'])->toHaveKey('updatedItem')
        ->and($parsedResponse['result']['updatedItem'])->toBeArray()
        ->and($parsedResponse['result']['updatedItem'])->toHaveKey('name')
        ->and($parsedResponse['result']['updatedItem']['name'])->toEqual('newNamdde.txt');
    Event::assertDispatched(ItemRenamed::class);
});

test('should delete a given file', function () {
    $itemService = new ItemService();
    Storage::disk('tests')->put('ios/test.txt', '');

    $response = $itemService->delete('tests', [
        'items' => [
            [
                'name' => 'test.txt',
                'path' => 'ios/test.txt',
                'type' => 'file'
            ],
        ]
    ]);

    Storage::disk('tests')->assertMissing('ios/test.txt');
    expect($response->getData(true))->toBeArray()
        ->and($response->getData(true))->toMatchArray([
            'status' => 'success',
            'message' => 'Items deleted successfully',
            'result' => []
        ]);
    Event::assertDispatched(ItemDeleted::class);
});

test('should upload a single item', function () {
    $itemService = new ItemService();

    $itemService->upload(
        'tests',
        [
            'ifFileExist' => 0,
            'destination' => 'ios',
            'items' => [
                UploadedFile::fake()->image('photo1.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertExists('ios/photo1.jpg');
    Event::assertDispatched(ItemUploaded::class);
});

test('should upload multiple items', function () {
    $itemService = new ItemService();

    $response = $itemService->upload(
        'tests',
        [
            'ifFileExist' => 0,
            'destination' => 'ios',
            'items' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
                UploadedFile::fake()->image('photo3.jpg'),
                UploadedFile::fake()->image('photo4.jpg')
            ]
        ]
    );

    $parsedResponse = $response->getData(true);
    foreach ($parsedResponse['result']['items'] as &$item) {
        unset($item['size'], $item['lastModified'], $item['formattedSize']);
    }
    Storage::disk('tests')->assertExists(['ios/photo1.jpg', 'ios/photo2.jpg', 'ios/photo3.jpg', 'ios/photo4.jpg']);
    expect($parsedResponse)->toBeArray()
        ->and($parsedResponse)->toMatchArray(
            [
                'status' => 'success',
                'message' => 'Items uploaded successfully',
                'result' => [
                    'items' => [
                        [
                            'diskName' => 'tests',
                            'parent' => 'ios',
                            'name' => 'photo1.jpg',
                            'path' => 'ios/photo1.jpg',
                            'type' => 'file',
                            'url' => '/storage/ios/photo1.jpg',
                            'extension' => 'jpg',
                            'isChecked' => false,
                            'subDir' => []
                        ],
                        [
                            'diskName' => 'tests',
                            'parent' => 'ios',
                            'name' => 'photo2.jpg',
                            'path' => 'ios/photo2.jpg',
                            'type' => 'file',
                            'extension' => 'jpg',
                            'url' => '/storage/ios/photo2.jpg',
                            'isChecked' => false,
                            'subDir' => []
                        ],
                        [
                            'diskName' => 'tests',
                            'parent' => 'ios',
                            'name' => 'photo3.jpg',
                            'path' => 'ios/photo3.jpg',
                            'type' => 'file',
                            'extension' => 'jpg',
                            'url' => '/storage/ios/photo3.jpg',
                            'isChecked' => false,
                            'subDir' => []
                        ],
                        [
                            'diskName' => 'tests',
                            'parent' => 'ios',
                            'name' => 'photo4.jpg',
                            'path' => 'ios/photo4.jpg',
                            'type' => 'file',
                            'extension' => 'jpg',
                            'url' => '/storage/ios/photo4.jpg',
                            'isChecked' => false,
                            'subDir' => []
                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(ItemUploaded::class);
});

test('should create a file', function () {
    $itemService = new ItemService();

    $response = $itemService->create('tests', [
        'destination' => 'ios',
        'path' => 'ios/zjztj.txt'
    ]);

    $parsedResponse = $response->getData(true);
    foreach ($parsedResponse['result']['items'] as &$item) {
        unset($item['size'], $item['lastModified'], $item['formattedSize']);
    }
    foreach ($parsedResponse['result']['dirs'] as &$item) {
        unset($item['size'], $item['lastModified'], $item['formattedSize']);
    }

    Storage::disk('tests')->assertExists('ios/zjztj.txt');
    expect($parsedResponse)->toBeArray()
        ->and($parsedResponse)->toMatchArray(
            [
                'status' => 'success',
                'message' => 'File created successfully',
                'result' => [
                    'items' => [
                        [
                            'diskName' => 'tests',
                            'parent' => 'ios',
                            'name' => 'zjztj.txt',
                            'path' => 'ios/zjztj.txt',
                            'type' => 'file',
                            'extension' => 'txt',
                            'url' => '/storage/ios/zjztj.txt',
                            'isChecked' => false,
                            'subDir' => []
                        ]
                    ],
                    'dirs' => [
                        [
                            "diskName" => "tests",
                            "parent" => "",
                            "name" => "ios",
                            "path" => "ios",
                            "type" => "dir",
                            "url" => "",
                            "extension" => '',
                            "isChecked" => false,
                            "subDir" => []
                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(FileCreated::class);
});

test('should return item content', function () {
    $item = createFakeFiles();
    $itemService = new ItemService();

    $result = $itemService->getItemContent(
        'tests',
        ['path' => $item[0]]
    );

    expect($result)->toBeString()
        ->and($result)->toBeEmpty();
});

test('should set new item content', function () {
    $item = createFakeFiles();
    $newtItemContent = 'new content';
    $itemService = new ItemService();

    $result = $itemService->updateItemContent(
        'tests',
        [
            'path' => $item[0],
            'item' => UploadedFile::fake()->createWithContent($item[0], $newtItemContent)->getContent(),
        ]
    );

    Storage::disk('tests')->assertExists($item[0]);
    $itemContent = Storage::disk('tests')->get($item[0]);

    expect($itemContent)->toBeString()
        ->and($itemContent)->toBe($newtItemContent)
        ->and($result->getData(true))->toBeArray()
        ->and($result->getData(true))->toMatchArray([
            'status' => 'success',
            'message' => 'Content updated successfully'
        ]);
});

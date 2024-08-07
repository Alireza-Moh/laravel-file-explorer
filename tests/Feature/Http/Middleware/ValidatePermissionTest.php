<?php

use AlirezaMoh\LaravelFileExplorer\Exceptions\NullUserException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Testing\Fluent\AssertableJson;

function assertResponse($response): void
{
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'status',
            'message',
            'result'
        ])
        ->where('status', 'failed')
        ->where('message', 'Could not validate user permission')
        ->where('result', [])
    );
}


beforeEach(function () {
    Exceptions::fake();
    Config::set([
        'laravel-file-explorer.acl_enabled' => true,
    ]);
});

test('should throw error when user does not have the __read__ permission to get item content', function () {
    $item = createFakeFiles();

    $response = $this->getJson(
        route(
            'fx.get-item-content',
            [
                'diskName' => 'tests'
            ]
        )
        . '?path=' . urlencode($item[0])
    );

    assertResponse($response);
});

test('should throw error when user does not have the __create__ permission to create a file', function () {
    $response = $this->postJson(
        route(
            'fx.file-create',
            ['diskName' => 'tests']
        ),
        [
            'destination' => 'ios',
            'path' => 'ios/config.txt'
        ]
    );

    Storage::disk('tests')->assertMissing('ios/config.txt');
    assertResponse($response);
});

test('should throw error when user does not have the __create__ permission to create a dir', function () {
    $response = $this->postJson(
        route(
            'fx.dir-create',
            ['diskName' => 'tests']
        ),
        [
            'destination' => 'ios',
            'path' => 'ios/configDir'
        ]
    );

    Storage::disk('tests')->assertMissing('ios/configDir');
    assertResponse($response);
});

test('should throw error when user does not have the __upload__ permission to upload files', function () {
    $response = $this->postJson(
        route(
            'fx.items-upload',
            ['diskName' => 'tests']
        ),
        [
            'ifItemExist' => 0,
            'destination' => 'ios',
            'items' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertMissing(['ios/photo1.jpg', 'ios/photo2.jpg']);
    assertResponse($response);
});

test('should throw error when user does not have the __download__ permission to download files', function () {
    $images = createFakeImages(2);

    $response = $this->postJson(
        route(
            'fx.items-download',
            ['diskName' => 'tests']
        ),
        [
            'items' => [
                [
                    'name' => $images[0],
                    'path' => 'ios/' . $images[0],
                    'type' => 'file'
                ],
                [
                    'name' => $images[1],
                    'path' => 'ios/' . $images[1],
                    'type' => 'file'
                ]
            ]
        ]
    );

    assertResponse($response);
});

test('should throw error when user does not have the __update__ permission to file item', function () {
    $images = createFakeImages();
    $response = $this->postJson(
        route(
            'fx.item-rename',
            [
                'diskName' => 'tests'
            ]
        ),
        [
            'oldName' => $images[0],
            'newName' => 'newName.png',
            'newPath' => 'ios/newName.png',
            'oldPath' => 'ios/' . $images[0],
            'type' => 'file',
            'parent' => 'ios'
        ]
    );

    Storage::disk('tests')->assertMissing('ios/newName.png');
    assertResponse($response);
});

test('should throw error when user does not have the __write__ permission to write into a file', function () {
    $item = createFakeFiles();
    $newtItemContent = 'new content';
    $response = $this->postJson(
        route(
            'fx.update-item-content',
            [
                'diskName' => 'tests',
                'itemName', $item[0]
            ]
        ),
        [
            'path' => $item[0],
            'item' => UploadedFile::fake()->createWithContent($item[0], $newtItemContent),
        ]
    );

    assertResponse($response);
});

test('should throw error when user does not have the __delete__ permission to delete an item', function () {
    $images = createFakeImages();

    $response = $this->post(
        route(
            'fx.items-delete',
            ['diskName' => 'tests']
        ),
        [
            'items' => [
                [
                    'name' => $images[0],
                    'path' => 'ios/' . $images[0],
                    'type' => 'file'
                ]
            ]
        ]
    );

    Storage::disk('tests')->assertExists('ios/' . $images[0]);
    assertResponse($response);
});

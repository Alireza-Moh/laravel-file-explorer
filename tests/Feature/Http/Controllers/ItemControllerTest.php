<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

test('should throw an error when disk does not exist', function () {
    $response = $this->postJson(
        route(
            'fx.file-create',
            ['diskName' => 'aa']
        ),
        [
            'destination' => 'ios',
            'path' => 'ios/config.txt'
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

test('should create file and return success response with all file inside the directory', function () {
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

    Storage::disk('tests')->assertExists('ios/config.txt');
    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', 'File created successfully')
            ->has('result')
            ->hasAll([
                'result.items',
                'result.dirs'
            ])
            ->has('result.items')
            ->has('result.dirs')
            ->has('result.items.0', fn(AssertableJson $json) =>
                $json->where('name', 'config.txt')
                    ->where('name', 'config.txt')
                    ->where('path', 'ios/config.txt')
                    ->where('type', 'file')
                    ->where('size', 0)
                    ->where('extension', 'txt')
                    ->where('url', '')
                    ->etc()
            )
    );
});

test('should throw an error when path is missing in the form data for creating a file', function () {
    $response = $this->postJson(
        route(
            'fx.file-create',
            ['diskName' => 'tests']
        ),
        [
            'destination' => 'ios',
            'type' => 'file',
            'dirPath' => 'ios'
            //'path' => 'ios/config.txt'
        ]
    );

    Storage::disk('tests')->assertMissing('ios/config.txt');
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->has('errors')
        ->has('errors.path')
        ->where('errors.path.0', 'File path is required')
        ->etc()
    );
});

test('should upload item or items and return success response with all items inside the directory', function () {
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

    Storage::disk('tests')->assertExists(['ios/photo1.jpg', 'ios/photo2.jpg']);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', 'Items uploaded successfully')
                ->has('result')
            ->hasAll([
                'result.items'
            ])
            ->has('result.items')
            ->has('result.items.0', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'photo1.jpg')
                ->where('path', 'ios/photo1.jpg')
                ->where('type', 'file')
                ->where('extension', 'jpg')
                ->where('url', '')
                ->etc()
            )
            ->has('result.items.1', fn(AssertableJson $json) =>
            $json->where('diskName', 'tests')
                ->where('name', 'photo2.jpg')
                ->where('path', 'ios/photo2.jpg')
                ->where('type', 'file')
                ->where('extension', 'jpg')
                ->where('url', '')
                ->etc()
            )
    );
});

test('should throw an error when ifItemExist is missing in the form while uploading items', function () {
    $response = $this->postJson(
        route(
            'fx.items-upload',
            ['diskName' => 'tests']
        ),
        [
            //'ifItemExist' => 0,
            'destination' => 'ios',
            'items' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]
        ]
    );

    Storage::disk('tests')->assertMissing(['ios/photo1.jpg', 'ios/photo2.jpg']);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->where(
            'errors',
            [
                'ifItemExist' => ['Choose an action overwrite/skip'],
            ]
        )
    );
});

test('should throw an error when items have wrong extension while uploading items', function () {
    $response = $this->postJson(
        route(
            'fx.items-upload',
            ['diskName' => 'tests']
        ),
        [
            'ifItemExist' => 0,
            'destination' => 'ios',
            'items' => [
                UploadedFile::fake()->create('doc1.pdf'),
                UploadedFile::fake()->create('doc2.pdf')
            ]
        ]
    );

    Storage::disk('tests')->assertMissing(['ios/doc1.pdf', 'ios/doc2.pdf']);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->where(
            'errors',
            [
                'doc1.pdf' => ['File extension not allowed'],
                'doc2.pdf' => ['File extension not allowed']
            ]
        )
    );
});

test('should download a single item', function () {
    $image = createFakeImages();

    $response = $this->postJson(
        route(
            'fx.items-download',
            ['diskName' => 'tests']
        ),
        [
            'items' => [
                [
                    'name' => $image[0],
                    'path' => 'ios/' . $image[0],
                    'type' => 'file',
                ]
            ]
        ]
    );

    $response->assertDownload();
});

test('should download multiple items as a ZIP file', function () {
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

    $response->assertDownload();
    $response->assertHeader('Content-Type', 'application/zip');
    $response->assertHeader('Content-Disposition', 'attachment; filename=tests.zip');
});

test('should rename a file', function () {
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

    Storage::disk('tests')->assertExists('ios/newName.png');
    $response->assertJson(fn (AssertableJson $json) =>
    $json->where('status', 'success')
        ->where('message', 'Item renamed successfully')
        ->etc()
    );
});

test('should throw an error when something is missing in form for renaming a item', function () {
    $response = $this->postJson(
        route(
            'fx.item-rename',
            ['diskName' => 'tests', 'dirName' => 'ios']
        ),
        [
            'newPath' => 'ios/newName.png',
            //'oldPath' => 'ios/oldName.png',
        ]
    );

    Storage::disk('tests')->assertMissing('ios/newName.png');
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->has('errors')
        ->has('errors.oldPath')
        ->where('errors.oldPath.0', 'Old file/directory path is required')
    );
});

test('should delete one file', function () {
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

    Storage::disk('tests')->assertMissing('ios/' . $images[0]);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', 'Items deleted successfully')
            ->where('result', [])
    );
});

test('should delete multiple items', function () {
    $images = createFakeImages(9);
    $dirs = createFakeDirs();

    $itemsToDelete = [];
    foreach ($images as $image) {
        $itemsToDelete[] = [
            'name' => $image,
            'path' => 'ios/' . $image,
            'type' => 'file',
        ];
    }
    $itemsToDelete[] = [
        'name' => $dirs[0]['name'],
        'path' => $dirs[0]['path'],
        'type' => 'dir',
    ];

    $response = $this->post(
        route(
            'fx.items-delete',
            ['diskName' => 'tests']
        ),
        [
            'items' => $itemsToDelete
        ]
    );

    $paths = array_column($itemsToDelete, 'path');
    Storage::disk('tests')->assertMissing($paths);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', 'Items deleted successfully')
            ->where('result', [])
    );
});

test('should throw an error when something is missing in form for deleting a item', function () {
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
                    'type' => 'file'
                    //'path' => 'ios/' . $images[0]
                ]
            ]
        ]
    );

    Storage::disk('tests')->assertExists('ios/' . $images[0]);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
    );
});

test('should get item content', function () {
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

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('status')
            ->has('message')
            ->has('result')
                ->hasAll([
                    'result.content'
                ])
                ->where('result.content', '')
            );
});

test('should throw error when item path is missing', function () {
    createFakeFiles();

    $response = $this->getJson(
        route(
            'fx.get-item-content',
            [
                'diskName' => 'tests'
            ]
        )
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'errors'
        ])
        ->where('errors.path.0', 'File or Directory path is missing')
    );
});

test('should update item content', function () {
    $item = createFakeFiles();

    $newtItemContent = 'new content';
    $response = $this->postJson(
        route(
            'fx.update-item-content',
            [
                'diskName' => 'tests',
                'itemName', 'fake_file_0.txt'
            ]
        ),
        [
            'path' => $item[0],
            'item' => UploadedFile::fake()->createWithContent($item[0], $newtItemContent),
        ]
    );

    $itemContent = Storage::disk('tests')->get($item[0]);
    expect($itemContent)->toBe($newtItemContent);
    $response->assertJson(fn (AssertableJson $json) =>
        $json->where('status', 'success')
            ->where('message', 'Content updated successfully')
            ->etc()
    );
});

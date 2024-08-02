<?php

use AlirezaMoh\LaravelFileExplorer\Events\DirCreated;
use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

test('should create a directory', function () {
    Event::fake();
    $dirService = new DirService();

    $response = $dirService->create('tests', [
        'destination' => 'ios',
        'path' => 'ios/zjztj'
    ]);

    $responseData = $response->getData(true);
    unset($responseData['result']['items'][0]['lastModified']);
    unset($responseData['result']['dirs'][0]['lastModified']);
    unset($responseData['result']['dirs'][0]['subDir'][0]['lastModified']);
    Storage::disk('tests')->assertExists('ios/zjztj');
    expect($responseData)->toBeArray()
        ->and($responseData)->toMatchArray(
            [
                'status' => 'success',
                'message' => 'Directory created successfully',
                'result' => [
                    'items' => [
                        [
                            "diskName" => "tests",
                            "parent" => "ios",
                            "name" => "zjztj",
                            "path" => "ios/zjztj",
                            "type" => "dir",
                            "size" => 0,
                            "formattedSize" => "-",
                            "url" => "/storage/ios/zjztj",
                            "extension" => null,
                            "isChecked" => false,
                            "subDir" => []
                      ]
                    ],
                    'dirs' => [
                        [
                            'diskName' => 'tests',
                            'parent' => '',
                            'name' => 'ios',
                            'path' => 'ios',
                            'type' => 'dir',
                            'size' => 0,
                            'formattedSize' => '-',
                            'url' => '/storage/ios',
                            'extension' => null,
                            'isChecked' => false,
                            'subDir' => [
                               [
                                   "diskName" => "tests",
                                   "parent" => "ios",
                                   "name" => "zjztj",
                                   "path" => "ios/zjztj",
                                   "type" => "dir",
                                   "size" => 0,
                                   "formattedSize" => "-",
                                   "url" => "/storage/ios/zjztj",
                                   "extension" => null,
                                   "isChecked" => false,
                                   "subDir" => []
                               ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    Event::assertDispatched(DirCreated::class);
});

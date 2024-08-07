<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('should throw an error when disk does not exist', function () {
    $response = $this->getJson(
        route(
            'fx.disks',
            ['diskName' => 'aa']
        )
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

test('should retrieve an empty disk information when requesting a disk with no content', function () {
    $response = $this->getJson(
        route(
            'fx.disks',
            ['diskName' => 'tests']
        )
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll([
            'status',
            'message',
            'result'
        ])
        ->where('result.dirs', [])
        ->where('result.selectedDir', '')
        ->where('result.selectedDirPath', '')
        ->where('result.selectedDirItems', [])
    );
});

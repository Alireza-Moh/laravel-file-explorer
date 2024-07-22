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
    $json->has('message')
        ->has('errors')
        ->where('message', 'Invalid data sent')
        ->where('errors.0.diskName', 'Disk aa does not exist')
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
        $json->has('result')
            ->where('result.dirs', [])
            ->where('result.selectedDir', '')
            ->where('result.selectedDirPath', '')
            ->where('result.selectedDirItems', [])
    );
});

<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('should retrieve an empty disk information when requesting a disk with no content', function () {
    $response = $this->getJson(
        route(
            "fx.disks",
            ["diskName" => "tests"]
        )
    );

    $response->assertJson(fn (AssertableJson $json) =>
        $json->where("dirs", [])
            ->where("selectedDir", "")
            ->where('selectedDirPath', "")
            ->where("selectedDirItems", [])
    );
});

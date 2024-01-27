<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('should load file explorer initial data', function () {
    $response = $this->getJson(route("fx.init-file-explorer"));

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('result')
            ->hasAll([
                "result.status",
                "result.data.disks",
                "result.data",
                "result.data.dirsForSelectedDisk",
                "result.data.selectedDisk",
                "result.data.selectedDirPath",
                "result.data.selectedDirItems"
            ])
            ->where("result.status", "success")
            ->where("result.data.disks", ["tests", "web", "images"])
            ->has('result.data.dirsForSelectedDisk')
            ->where("result.data.dirsForSelectedDisk.dirs", [])
            ->where("result.data.dirsForSelectedDisk.diskName", "tests")
            ->where("result.data.selectedDisk", "tests")
            ->where("result.data.selectedDir", "ios")
            ->where("result.data.selectedDirPath", "")
            ->where("result.data.selectedDirItems", [])
    );
});

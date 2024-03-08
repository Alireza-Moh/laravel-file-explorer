<?php

use AlirezaMoh\LaravelFileExplorer\Supports\Zipper;

test('should create a zip file successfully', function () {
    $images = createFakeImages(2);
    $items = [
        ['path' => "ios/" . $images[0]],
        ['path' => "ios/" . $images[1]],
    ];

    $zipper = new Zipper("tests", "test.zip", $items);
    $zipper->zip();

    expect(file_exists($zipper->getZipPath()))->toBeTrue();
});

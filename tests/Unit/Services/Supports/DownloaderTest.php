<?php

use Alireza\LaravelFileExplorer\Services\Supports\Downloader;

test('should download a single item', function () {
    $image = createFakeImages();
    $downloader = new Downloader(
        "tests",
        [
            [
                "name" => $image[0],
                "path" => "ios/" . $image[0],
                "type" => "file",
            ]
        ]
    );

    $response = $downloader->download();

    $headers = $response->headers;
    expect($headers->get("Content-Type"))->toBe("image/png")
        ->and($headers->get("Content-Disposition"))->toBe("attachment; filename=fake_image_0.png");
});

test('should download multiple items as a ZIP file', function () {
    $images = createFakeImages(2);
    $downloader = new Downloader(
        "tests",
        [
            [
                "name" => $images[0],
                "path" => "ios/" . $images[0],
                "type" => "file"
            ],
            [
                "name" => $images[1],
                "path" => "ios/" . $images[1],
                "type" => "file"
            ]
        ]
    );

    $response = $downloader->download();

    expect($response->headers->get("Content-Disposition"))->toBe("attachment; filename=tests.zip");
});

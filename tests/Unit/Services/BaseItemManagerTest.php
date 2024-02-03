<?php

use Alireza\LaravelFileExplorer\Services\BaseItemManager;
$targetAbstractClass = null;

beforeEach(function () {
    $this->targetAbstractClass = new class extends BaseItemManager {
        public function getResponse(bool $result, string $success = "", string $failure = "", array $additionalData = []): array
        {
            return parent::getResponse($result, $success, $failure, $additionalData);
        }
    };
});

test('should return success message when operation successes', function () {
    $response = $this->targetAbstractClass->getResponse(true, success: "This is a success message");

    expect($response)->toBeArray()
        ->and($response)->toMatchArray(
            [
                "result" => [
                    "status" => "success",
                    "message" => "This is a success message"

                ]
            ]
        );
});

test('should return failure message when operation failed', function () {
    $response = $this->targetAbstractClass->getResponse(false, failure: "This is a failure message");

    expect($response)->toBeArray()
        ->and($response)->toMatchArray(
            [
                "result" => [
                    "status" => "failed",
                    "message" => "This is a failure message"

                ]
            ]
        );
});

test('should return a response with the custom data', function () {
    $response = $this->targetAbstractClass->getResponse(
        true,
        success: "This is a success message",
        additionalData: [
            "items" => [],
        ]
    );

    expect($response)->toBeArray()
        ->and($response)->toMatchArray(
            [
                "result" => [
                    "status" => "success",
                    "message" => "This is a success message",
                    "items" => []
                ]
            ]
        );
});

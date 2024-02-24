<?php

namespace Alireza\LaravelFileExplorer\Exceptions;

use Exception;
use ZipArchive;

class ZipperException extends Exception
{
    /**
     * ZipperException constructor
     *
     * @param int $errorCode The ZipArchive error code
     */
    public function __construct(int $errorCode)
    {
        parent::__construct();
        $this->message = $this->makeHumanFriendly($errorCode);
    }

    /**
     * Converts a ZipArchive error code to a human-readable message
     *
     * @param int $errorCode The ZipArchive error code.
     * @return string error message
     */
    public function makeHumanFriendly(int $errorCode): string
    {
        return match ($errorCode) {
            ZipArchive::ER_EXISTS => 'ZipArchive::ER_EXISTS - File already exists.',
            ZipArchive::ER_INCONS => 'ZipArchive::ER_INCONS - Zip archive inconsistent.',
            ZipArchive::ER_MEMORY => 'ZipArchive::ER_MEMORY - Malloc failure.',
            ZipArchive::ER_NOENT => 'ZipArchive::ER_NOENT - No such file.',
            ZipArchive::ER_NOZIP => 'ZipArchive::ER_NOZIP - Not a zip archive.',
            ZipArchive::ER_OPEN => 'ZipArchive::ER_OPEN - Can not open file.',
            ZipArchive::ER_READ => 'ZipArchive::ER_READ - Read error.',
            ZipArchive::ER_SEEK => 'ZipArchive::ER_SEEK - Seek error.',
            default => "An unknown error [$errorCode] has occurred.",
        };
    }
}

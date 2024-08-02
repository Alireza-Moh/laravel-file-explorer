<?php

namespace AlirezaMoh\LaravelFileExplorer\Exceptions;

use Exception;
use ZipArchive;

class ZipperException extends Exception
{
    public function __construct(int $errorCode)
    {
        parent::__construct();
        $this->message = $this->makeHumanFriendly($errorCode);
    }

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

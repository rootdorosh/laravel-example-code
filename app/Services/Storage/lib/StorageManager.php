<?php
declare( strict_types = 1 );

namespace App\Services\Storage\lib;

use Illuminate\Filesystem\FilesystemManager;

/**
 * Class StorageService
 * @package App\Services\Storage
 */
abstract class StorageManager
{
    /**
     * @var FilesystemManager
     */
    public $storageManager;

    /**
     * StorageService constructor.
     * @param StorageManagerHandler $storageManagerHandler
     */
    public function __construct(StorageManagerHandler $storageManagerHandler)
    {
        $this->storageManager = $storageManagerHandler->getManager();
    }

    /**
     * @param string $file
     * @param string $fileName
     * @return string
     */
    public function save(string $file, string $fileName) : string
    {
        $filePath = $this->getStoragePath() . $fileName;

        $this->storageManager->put($filePath, $file);

        return $this->storageManager->url($filePath);
    }

    /**
     * @return string
     */
    abstract public function getStoragePath() : string;
}

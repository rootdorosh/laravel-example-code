<?php
declare( strict_types = 1 );

namespace App\Services\Storage;

use App\Services\Storage\lib\StorageManager;

/**
 * Class ImageStorageManager
 * @package App\Services\Storage
 */
class ImageStorageManager extends StorageManager
{
    /**
     * @return string
     */
    public function getStoragePath() : string
    {
        return date('y') . '/' . date('m') . '/' . date('d') . '/';
    }
}

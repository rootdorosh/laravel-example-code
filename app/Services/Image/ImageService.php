<?php
declare( strict_types = 1 );

namespace App\Services\Image;

use App\Services\Storage\ImageStorageManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

/**
 * Class ImageService
 * @package App\Services\Image
 */
class ImageService implements ImageManagerInterface
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var ImageStorageManager
     */
    private $storageManager;
    
    /**
     * @var Repository
     */
    private $configRepository;

    /**
     * ImageService constructor.
     * @param ImageManager        $imageManager
     * @param ImageStorageManager $storageManager
     * @param Repository          $repository
     */
    public function __construct(
        ImageManager $imageManager,
        ImageStorageManager $storageManager,
        Repository $repository
    ) {
        $this->imageManager     = $imageManager;
        $this->storageManager   = $storageManager;
        $this->configRepository = $repository;
    }

    /**
     * @param mixed  $data
     * @param string|null $imageName
     * @return string
     */
    public function save($data, string $imageName = null) : string
    {
        $image = $this->prepareImage($data);

        if (!$imageName) {
            $imageName = md5_file($data);
        }

        $filePath = $this->storageManager->save($image->stream()
                                                      ->getContents(), $imageName);

        return $filePath;
    }

    /**
     * @param mixed $data
     * @return Image
     */
    private function prepareImage($data) : Image
    {
        $image = $this->imageManager->make($data);

        $resizeData = $this->configRepository->get('image.resize');

        //$image->resize($resizeData[ 'width' ], $resizeData[ 'height' ]);

        return $image;
    }

    /**
     * @param UploadedFile $imageFile
     * @param array $params
     * @return string
     */
    public function upload(UploadedFile $imageFile, array $params = []) : string
    {
        $name = strtolower(Str::random(8));
        
        $fileName = $name . '.' . $imageFile->extension();
        
        return $this->save($imageFile, $fileName);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function deleteOld(string $path)
    {
    }
}

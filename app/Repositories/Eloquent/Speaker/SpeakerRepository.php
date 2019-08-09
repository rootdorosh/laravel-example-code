<?php
declare( strict_types = 1 );

namespace App\Repositories\Eloquent\Speaker;

use Illuminate\Support\Collection;
use App\Models\Speaker;
use App\Repositories\Interfaces\Speaker\SpeakerRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Services\Image\ImageManagerInterface;

/**
 * Class SpeakerRepository
 * @package App\Repositories\Eloquent\Speaker
 */
class SpeakerRepository extends AbstractRepository implements SpeakerRepositoryInterface
{
    /**
     * @var ImageManagerInterface
     */
    private $imageService;

    /**
     * SpeakerRepository constructor.
     *
     * @param ImageManagerInterface $imageService
     */
    public function __construct(ImageManagerInterface $imageService)
    {
        $this->imageService = $imageService;

        parent::__construct(Speaker::class);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data) : Speaker
    {
        $data = $this->saveMedia($data);
       
        $speaker = $this->model::create($data);
        
        return $speaker;
    }

    /**
     * @param array $data
     * @param Speaker  $speaker
     * @return Speaker
     */
    public function update(array $data, Speaker $speaker) : Speaker
    {
        $data = $this->saveMedia($data);
        
        $speaker->update($data);

        return $speaker;
    }

    /**
     * @param Speaker $speaker
     * @return bool
     */
    public function destroy(Speaker $speaker) : bool
    {
        return $speaker->delete();
    }
    
    /**
     *  Save files.
     *
     * @param array $data
     * @return array
     */
    private function saveMedia(array $data) : array
    {
        if (!empty($data['image_file'])) {
            $data['image'] = $this->imageService->upload($data['image_file']);
        }

        return $data;
    }
}

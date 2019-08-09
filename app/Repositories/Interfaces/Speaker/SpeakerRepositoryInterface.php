<?php
declare( strict_types = 1 );

namespace App\Repositories\Interfaces\Speaker;

use App\Models\Speaker;
use Illuminate\Support\Collection;

/**
 * Interface SpeakerRepositoryInterface
 * @package App\Repositories\Interfaces\Speaker
 */
interface SpeakerRepositoryInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data) : Speaker;

    /**
     * @param array $data
     * @param Speaker  $speaker
     * @return Speaker
     */
    public function update(array $data, Speaker $speaker) : Speaker;

    /**
     * @param Speaker $speaker
     * @return bool
     */
    public function destroy(Speaker $speaker) : bool;
}

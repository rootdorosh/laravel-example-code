<?php
namespace App\Transformers;

use App\Models\Speaker;
use App\Transformers\Traits\Pollable;

/**
 * Class SpeakerTransformer.
 */
class SpeakerTransformer extends AbstractTransformer
{
    use Pollable;
    
    /**
     * default includes
     *
     * @var array
     */
    protected $defaultIncludes = [
        'has_bookmark',
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'facebook',
        'email',
        'biography',
        'activities',
        'documents',
        'has_bookmark',
    ];
    
    /**
     * List of item resource to include
     *
     * @var array
     */
    public $itemIncludes = [
        'email',
        'biography',
        'facebook',
        'has_bookmark',
        'activities',
        'pollables',
        'documents',
    ];

    /**
     * transform
     *
     * @param Speaker $speaker
     * @return array
     */
    public function transform($speaker)
    {
        return [
            'id' => $speaker->id,
            'image' => $speaker->getThumb('image'),
            'name' => $speaker->name,
        ];
    }
    
    /**
     * Include email
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmail(Speaker $speaker)
    {
        return $this->primitive($speaker->email);
    }

    /**
     * Include facebook
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeFacebook(Speaker $speaker)
    {
        return $this->primitive($speaker->facebook);
    }
    
    /**
     * Include biography
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeBiography(Speaker $speaker)
    {
        return $this->primitive($speaker->biography);
    }
    
    /**
     * Include has_bookmark
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeHasBookmark(Speaker $speaker)
    {
        return $this->primitive($speaker->has_bookmark);
    }
    
    /**
     * Include polls
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includePollables(Speaker $speaker)
    {
        return $this->collection($this->getPolls($speaker), new PollableTransformer);
    }
    
    /**
     * Include documents
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeDocuments(Speaker $speaker)
    {
        return $this->collection($speaker->getDocuments($this->params['event_id']), new EventActivityDocumentTransformer);
    }
    
    /**
     * Include activities
     *
     * @param Speaker $speaker
     * @return \League\Fractal\Resource\Item
     */
    public function includeActivities(Speaker $speaker)
    {
        return $this->collection(
            $speaker->getActivities($this->params['event_id']),
            (new EventActivityTransformer)->setDefaultIncludes(['location'])
        );
    }
}

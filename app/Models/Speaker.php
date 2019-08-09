<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Dimsav\Translatable\Translatable;
use App\Services\Image\Thumbnailable;
use App\Models\Interfaces\PollableInterface;

class Speaker extends Model implements PollableInterface
{
    use Translatable, Thumbnailable;

    public $translatedAttributes = ['name', 'biography'];

    protected $with = ['translations'];
    
    public $translationForeignKey = 'speaker_id';

    public $timestamps = false;
    
    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'speakers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'facebook',
        'image',
    ];
   
    
    /**
     * Get all of the speaker polls.
     *
     * @return MorphMany
     */
    public function polls() : MorphMany
    {
        return $this->morphMany('App\Models\Pollable', 'pollable');
    }
    
    /**
     * @return BelongsToMany
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Models\EventActivity',
            'events_activities_vs_speakers',
            'speaker_id',
            'activity_id'
        );
    }
    
    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Models\EventActivityDocument',
            'events_activities_documents_vs_speakers',
            'speaker_id',
            'document_id'
        );
    }
    
    /**
     * @param int $event_id
     * @return Collection
     */
    public function getDocuments(int $event_id): Collection
    {
        return $this->documents()
            ->leftJoin('events_activities', function ($join) use ($event_id) {
                $join->on('events_activities_documents.activity_id', '=', 'events_activities.id');
            })
            ->where('events_activities.event_id', '=', $event_id)
            ->get();
    }
    
    /**
     * @param int $event_id
     * @return Collection
     */
    public function getActivities(int $event_id): Collection
    {
        return $this->activities()
            ->where('events_activities.event_id', '=', $event_id)
            ->get();
    }
    
     /**
     * @return int
     */
    public function getHasBookmarkAttribute($value) : int
    {
        if ($value === null) {
            return auth()->user()->speakersBookmarks->contains($this->id) ? 1 : 0;
        }
        
        return $value;
    }
    
}

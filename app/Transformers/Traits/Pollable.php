<?php

namespace App\Transformers\Traits;

use App\Transformers\PollableTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DB;

trait Pollable
{
    /**
     * Get polls
     *
     * @param  Model $owner
     * @return Collection
     */
    public function getPolls(Model $owner) : Collection
    {
        $time = time();
        $where = "(from_at IS NULL OR from_at < {$time}) AND (to_at IS NULL OR to_at > {$time})";
        
        return $owner->polls()
            ->with(['poll'])
            ->select([
                'pollable.*',
                DB::raw('IF(pollable_reply.user_id IS NULL, 0, 1) AS already_voted'),
            ])
            ->leftJoin('pollable_reply', function ($join) {
                $join->on('pollable.id', '=', 'pollable_reply.pollable_id');
                $join->where('pollable_reply.user_id', '=', auth()->user()->id);
            })
            ->whereRaw($where)->orderBy('from_at')->get();
    }
}

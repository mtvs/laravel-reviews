<?php

namespace Mtvs\Reviews;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Mtvs\EloquentApproval\Approvable;

trait ReviewConcerns
{
	use Approvable;

	/**
	 * Get the relation to the user model
	 *
	 * @return BelongsTo
	 **/
	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}

	/**
	 * Get the relation to the reviewable model
	 * 
	 * @return MorphTo
	 **/
	public function reviewable()
	{
		return $this->morphTo();
	}

	/**
	 * Add the query scope for the reviewable model
	 * 
	 * @param Builder $query
	 * @return void
	 **/
	public function scopeReviewable($query, Model $reviewable)
	{
		$query->whereMorphedTo($this->reviewable(), $reviewable);
	}	
}

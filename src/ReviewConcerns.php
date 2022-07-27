<?php

namespace Mtvs\Reviews;

use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentApproval\Approvable;

trait ReviewConcerns
{
	use Approvable;

	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}

	public function reviewable()
	{
		return $this->morphTo();
	}

	public function scopeReviewable($query, Model $reviewable)
	{
		$query->whereMorphedTo($this->reviewable(), $reviewable);
	}	
}

<?php

namespace Reviews;

use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentApproval\Approvable;

trait ReviewConcerns
{
	use Approvable;

	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}

	public function scopeReviewable($query, Model $reviewable)
	{
		$query->where([
			'reviewable_type' => get_class($reviewable),
			'reviewable_id' => $reviewable->getKey(),
		]);
	}	
}

<?php

namespace Reviews;

use Illuminate\Database\Eloquent\Model;

trait PerformsReviews 
{
	public function reviews()
	{
		return $this->hasMany(app('review_class'));
	}

	public function hasAlreadyReviewed(Model $reviewable): bool
	{
		return $this->reviews()->where([
			'reviewable_type' => get_class($reviewable),
			'reviewable_id' => $reviewable->getKey(),
		])->count() > 0;
	}
}

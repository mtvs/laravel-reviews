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
		return $this->reviews()
			->anyApprovalStatus()
			->reviewable($reviewable)
			->count() > 0;
	}

	public function getReviewfor(Model $reviewable): Model|null
	{
		return $this->reviews()
		->anyApprovalStatus()
		->reviewable($reviewable)
		->first();
	}
}

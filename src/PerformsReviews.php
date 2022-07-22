<?php

namespace Reviews;

use Illuminate\Database\Eloquent\Model;

trait PerformsReviews 
{
	public static function bootPerformsReviews()
	{
		static::deleted(function ($model) {
			if (! $model->exists) {
				$model->reviews()->forceDelete();
			}
		});
	}

	public function reviews()
	{
		return $this->hasMany(config('reviews.model'));
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

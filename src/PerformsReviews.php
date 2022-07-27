<?php

namespace Mtvs\Reviews;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait PerformsReviews 
{
	/**
	 * Boot the PerformsReviews trait
	 * 
	 * @return void
	 **/
	public static function bootPerformsReviews()
	{
		static::deleted(function ($model) {
			if (! $model->exists) {
				$model->reviews()->forceDelete();
			}
		});
	}

	/**
	 * Get the relation to the reviews model
	 * 
	 * @return HasMany
	 **/
	public function reviews()
	{
		return $this->hasMany(config('reviews.model'));
	}

	/**
	 * Determine if the user has already reviewed a reviewable model
	 * 
	 * @return boolean
	 **/
	public function hasAlreadyReviewed(Model $reviewable): bool
	{
		return $this->reviews()
			->anyApprovalStatus()
			->reviewable($reviewable)
			->count() > 0;
	}

	/**
	 * Get the user's review for a reviewable model
	 * 
	 * @return Model|null
	 **/
	public function getReviewfor(Model $reviewable): Model|null
	{
		return $this->reviews()
		->anyApprovalStatus()
		->reviewable($reviewable)
		->first();
	}
}

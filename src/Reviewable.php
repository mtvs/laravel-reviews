<?php

namespace Mtvs\Reviews;

use Illuminate\Support\Str;

trait Reviewable
{
	public static function bootReviewable()
	{
		static::deleted(function ($model) {
			if (! $model->exists) {
				$model->reviews()->forceDelete();
			}
		});
	}

	public function getRouteType()
	{
		$name = (new \ReflectionClass(get_called_class()))
			->getShortName();

		return Str::snake($name, '-');
	}

	public function reviews()
	{
		return $this->morphMany(config('reviews.model'), 'reviewable');
	}

	public function ratingRatios()
	{
		$ratios = [];

		$total = $this->reviews()->count();

		$this->reviews()->groupBy('rating')
			->selectRaw('rating, count(*) as count')
			->get()
			->each(function($group) use (&$ratios, $total) {
				$ratios[$group->rating] = round($group->count / $total * 100);
			});

		foreach(range(1, config('reviews.rating_max')) as $rating) {
			if (! array_key_exists($rating, $ratios)) {
				$ratios[$rating] = 0;
			}
		}

		ksort($ratios);

		return $ratios;
	}

	public function getRatingRatiosAttribute()
	{
		return $this->ratingRatios();
	}

	public function scopeHighestRated($query)
	{
		$totalAverage = config('reviews.model')::query()
			->where('reviewable_type', get_called_class())
			->whereHas('reviewable')
			->avg('rating');

		$confidenceNumber = $this->bayesianConfidenceNumber();

		$query->withRatings()
			->orderByRaw(
				'(IFNULL(ratings_avg, 0) * ratings_count + ? * ?)'.
				'/ (ratings_count + ?) DESC', 
				[$totalAverage, $confidenceNumber, $confidenceNumber]
			);
	}

	protected function bayesianConfidenceNumber()
	{
		return config('reviews.model')::query()
			->where('reviewable_type', get_called_class())
			->whereHas('reviewable')
			->count() / $this->count();;
	}

	public function scopeWithRatings($query)
	{
		$query->withAvg('reviews as ratings_avg', 'rating')
			->withCount('reviews as ratings_count');
	}

	public function loadRatings()
	{
		$this->ratings_avg = $this->reviews()->avg('rating');

		$this->ratings_count = $this->reviews()->count();
	}
}

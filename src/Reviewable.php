<?php

namespace Reviews;

use Illuminate\Support\Str;

trait Reviewable
{
	public function getRouteType()
	{
		$name = (new \ReflectionClass(get_called_class()))
			->getShortName();

		return Str::snake($name, '-');
	}

	public function reviews()
	{
		return $this->morphMany(app('review_class'), 'reviewable');
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

		foreach(range(1, app('review_class')::RATING_MAX) as $rating) {
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
		$reviewClass = app('review_class');

		$totalAverage = $reviewClass::where('reviewable_type', get_called_class())
		->avg('rating');
		$averageCount = $reviewClass::where('reviewable_type', get_called_class())
		->count() / $this->count();

		$query->withAvg('reviews as itemAverage', 'rating')
			->withCount('reviews as itemCount')
			->orderByRaw('(IFNULL(itemAverage, 0) * itemCount + ? * ?) / (itemCount + ?) DESC', [
				$totalAverage, $averageCount, $averageCount
			]);
	}

	public function scopeWithRatings($query)
	{
		$query->withAvg('reviews as ratings_avg', 'rating')
			->withCount('reviews as ratings_count');
	}
}

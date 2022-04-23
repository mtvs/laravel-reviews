<?php

namespace Reviews;

trait Reviewable
{
	public function reviews()
	{
		return $this->morphMany(app('review_class'), 'reviewable');
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

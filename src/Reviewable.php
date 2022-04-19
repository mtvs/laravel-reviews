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

		$totalAverage = $reviewClass::where('reviewable_type', get_class($this))
		->avg('rating');
		$averageCount = $reviewClass::where('reviewable_type', get_class($this))
		->count() / $this->count();

		$query->withAvg('reviews as itemAverage', 'rating')
			->withCount('reviews as itemCount')
			->orderByRaw('(IFNULL(itemAverage, 0) * itemCount + ? * ?) / (itemCount + ?) DESC', [
				$totalAverage, $averageCount, $averageCount
			]);
	}

	public function scopeWithRatings($query)
	{
		$query->withAvg('reviews as rating_avg', 'rating')
			->withCount('reviews as rating_count');
	}
}

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

		// todo: get the rating column from the review class
		$totalAverage = $reviewClass::where('reviewable_type', get_class($this))
		->avg('rating');
		$averageCount = $reviewClass::where('reviewable_type', get_class($this))
		->count() / $this->count();

		// todo: get the rating column from the review class
		$query->withAvg('reviews as itemAverage', 'rating')
			->withCount('reviews as itemCount')
			->orderByRaw('(IFNULL(itemAverage, 0) * itemCount + ? * ?) / (itemCount + ?) DESC', [
				$totalAverage, $averageCount, $averageCount
			]);
	}
}

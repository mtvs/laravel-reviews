<?php

namespace Mtvs\Reviews;

trait IndexesReviews
{
	public function index($reviewable_set, $reviewable_key)
	{
		$reviewable = $this->findReviewableOrFail(
			$reviewable_set, $reviewable_key
		);

		return $reviewable->reviews()->paginate();
	}

	protected function findReviewableOrFail($set, $key)
	{
		foreach (config('reviews.reviewables') as $reviewable) {
			if ((new $reviewable)->getSetName() == $set) {
				$class = $reviewable;
				break;
			}
		}

		if (! isset($class)) {
			abort(404);
		}

		return $class::findOrFail($key);;
	}
}

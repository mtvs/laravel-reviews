<?php

namespace Reviews;

trait IndexesReviews
{
	public function index($routetype, $key)
	{
		$reviewable = $this->findReviewableOrFail($routetype, $key);

		return $reviewable->reviews()->paginate();
	}

	protected function findReviewableOrFail($routetype, $key)
	{
		foreach (config('reviews.reviewables') as $reviewable) {
			if ((new $reviewable)->getRouteType() == $routetype) {
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

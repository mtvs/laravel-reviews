<?php

namespace Reviews;

trait PerformsReviews 
{
	public function reviews()
	{
		return $this->hasMany(app('review_class'));
	}
}

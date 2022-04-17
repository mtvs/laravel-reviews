<?php

namespace Reviews;

trait Reviewable
{
	public function reviews()
	{
		return $this->morphMany(app('review_class'), 'reviewable');
	}
}
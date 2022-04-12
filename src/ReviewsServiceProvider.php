<?php

namespace Reviews;

use Illuminate\Support\ServiceProvider;

class ReviewsServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('review_class', Review::class);
	}
}
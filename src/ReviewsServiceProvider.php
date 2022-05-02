<?php

namespace Reviews;

use Illuminate\Support\ServiceProvider;
use Reviews\Commands\ControllersCommand;

class ReviewsServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/reviews.php' => config_path('reviews.php')
		]);
	}

	public function register()
	{
		$this->app->bind('review_class', Review::class);

		if ($this->app->runningInConsole()) {
			$this->commands([
				ControllersCommand::class,
			]);
		}
	}
}

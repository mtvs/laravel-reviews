<?php

namespace Reviews;

use Illuminate\Support\ServiceProvider;
use Reviews\Commands\ControllersCommand;
use Illuminate\Support\Facades\Route;

class ReviewsServiceProvider extends ServiceProvider
{
	public function boot()
	{
		Route::mixin(new ReviewsRouteMethods);

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

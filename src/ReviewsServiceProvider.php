<?php

namespace Reviews;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Reviews\Commands\ControllersCommand;
use Reviews\View\Components\Reviews;

class ReviewsServiceProvider extends ServiceProvider
{
	public function boot()
	{
		Route::mixin(new ReviewsRouteMethods);

		$this->publishes([
			__DIR__.'/../config/reviews.php' => config_path('reviews.php')
		]);

		Blade::component('reviews', Reviews::class);
	}

	public function register()
	{
		$this->app->bind('review_class', 'App\Models\Review');

		if ($this->app->runningInConsole()) {
			$this->commands([
				ControllersCommand::class,
			]);
		}
	}
}

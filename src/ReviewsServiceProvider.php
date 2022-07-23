<?php

namespace Mtvs\Reviews;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Mtvs\Reviews\Commands\ReviewsUiCommand;
use Mtvs\Reviews\View\Components\Reviews;
use Mtvs\Reviews\View\Components\Ratings;

class ReviewsServiceProvider extends ServiceProvider
{
	public function boot()
	{
		Route::mixin(new ReviewsRouteMethods);

		$this->publishes([
			__DIR__.'/../config/reviews.php' => config_path('reviews.php'),

			__DIR__.'/../stubs/2022_03_25_000000_create_reviews_table.stub' =>
				database_path('migrations/2022_03_25_000000_create_reviews_table.php'),

			__DIR__.'/../stubs/Review.stub' => app_path('Models/Review.php'),

			__DIR__.'/../stubs/ReviewFactory.stub' => database_path('factories/ReviewFactory.php'),

			__DIR__.'/../stubs/ReviewsController.stub' => app_path('Http/Controllers/ReviewsController.php'),
		]);

		Blade::component('reviews', Reviews::class);

		Blade::component('ratings', Ratings::class);

		$this->loadViewsFrom(__DIR__.'/../resources/views', 'reviews');
	}

	public function register()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				ReviewsUiCommand::class,
			]);
		}
	}
}

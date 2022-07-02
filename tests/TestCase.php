<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Testing\TestResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Mtvs\EloquentApproval\ApprovalServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Reviews\ReviewsServiceProvider;
use Reviews\Tests\Models\Review;
use Reviews\Tests\Models\User;

abstract class TestCase extends Orchestra
{
	public function setup(): void
	{
		parent::setup();

		$this->withoutExceptionHandling();

		$this->app['config']->set('reviews.model', Review::class);
		$this->app['config']->set('auth.providers.users.model', User::class);
		$this->app['config']->set('reviews.reviewables', [
			'Reviews\Tests\Models\Product',
		]);

		$this->loadLaravelMigrations();
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadMigrationsFrom(__DIR__.'/./database/migrations');
	}

	protected function getPackageProviders($app)
	{
		return [
			ReviewsServiceProvider::class,
			ApprovalServiceProvider::class,
		];
	}

	protected function handleRequestUsing(Request $request, callable $callback)
	{
		try {
			$response = response(
				(new Pipeline($this->app))
					->send($request)
					->through([])
					->then($callback)
			);
		} catch (\Throwable $e) {
			$this->app[ExceptionHandler::class]
				->report($e);

			$response = $this->app[ExceptionHandler::class]
				->render($request, $e);
		}

		return new TestResponse($response);
	}
}

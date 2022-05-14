<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as Orchestra;
use Reviews\ReviewsServiceProvider;
use Reviews\Tests\Models\User;

abstract class TestCase extends Orchestra
{
	public function setup(): void
	{
		parent::setup();

		$this->app['config']->set('auth.providers.users.model', User::class);
		$this->app['config']->set('reviews.reviewables', [
			'\Reviews\Tests\Models\Product',
		]);

		$this->loadLaravelMigrations();
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadMigrationsFrom(__DIR__.'/./database/migrations');
	}

	protected function getPackageProviders($app)
	{
		return [ReviewsServiceProvider::class];
	}

	protected function handleRequestUsing(Request $request, callable $callback)
	{
		return new TestResponse(response(
			(new Pipeline($this->app))
				->send($request)
				->through([])
				->then($callback)
		));
	}
}

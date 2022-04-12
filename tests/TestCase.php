<?php

namespace Reviews\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Reviews\ReviewsServiceProvider;
use Reviews\Tests\Models\User;

abstract class TestCase extends Orchestra
{
	public function setup(): void
	{
		parent::setup();

		$this->app['config']->set('auth.providers.users.model', User::class);

		$this->loadLaravelMigrations();
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadMigrationsFrom(__DIR__.'/./database/migrations');
	}

	protected function getPackageProviders($app)
	{
		return [ReviewsServiceProvider::class];
	}
}

<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Testing\TestResponse;
use Reviews\Review;
use Reviews\HandlesReviews;
use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Database\Factories\UserFactory;

class HandlesReviewsTest extends TestCase
{
	use HandlesReviews;

	/** @test */
	public function it_can_create_a_review()
	{
		$user = UserFactory::new()->create();

		$data = Reviewfactory::new()->raw();

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = new TestResponse(
			(new Pipeline($this->app))
				->send($request)
				->through([
					// \Illuminate\Session\Middleware\StartSession::class,
				])
				->then(function ($request) {
					$this->create($request);
				})
		);

		$this->assertDatabaseHas('reviews', [
			'title' => $data['title'],
			'user_id' => $user->id
		]);
	}
}
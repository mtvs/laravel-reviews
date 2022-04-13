<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Testing\TestResponse;
use Reviews\Review;
use Reviews\HandlesReviews;
use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Database\Factories\productFactory;
use Reviews\Tests\Database\Factories\UserFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->create($request);
		});

		$this->assertDatabaseHas('reviews', [
			'title' => $data['title'],
			'user_id' => $user->id
		]);

		$response->assertSee($data['title']);
	}

	/** @test */
	public function it_rejects_creating_reviews_for_non_existing_reviewables()
	{
		$this->expectException(HttpException::class);

		$user = UserFactory::new()->create();

		$data = Reviewfactory::new()->raw([
			'reviewable_id' => 0,
		]);

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->create($request);
		});

		// $this->assertDatabaseMissing('reviews', [
		// 	'reviewable_id' => 0,
		// ]);

		// $response->assertStatus(422);
	}

	protected function handleRequestUsing(Request $request, callable $callback)
	{
		return new TestResponse(
			(new Pipeline($this->app))
				->send($request)
				->through([])
				->then($callback)
		);
	}
}
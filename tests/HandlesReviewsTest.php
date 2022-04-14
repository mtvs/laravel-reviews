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

	/** @test */
	public function it_rejects_creating_multiple_reviews_from_single_user()
	{
		$user = UserFactory::new()->create();

		$product = ProductFactory::new()->create();

		$user->reviews()->save(
			ReviewFactory::new()->make([
				'reviewable_type' => get_class($product),
				'reviewable_id' => $product->id,
			])
		);

		$data = Reviewfactory::new()->raw([
			'reviewable_type' => get_class($product),
			'reviewable_id' => $product->id,
		]);

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->create($request);
		});

		$this->assertDatabaseMissing('reviews', [
			'title' => $data['title'],
			'reviewable_type' => get_class($product),
			'reviewable_id' => $product->id,
			'user_id' => $user->id,
		]);		

		$response->assertStatus(403);
	}

	/** @test */
	public function it_can_update_a_review()
	{
		$user = UserFactory::new()->create();

		$review = $user->reviews()->save(
			ReviewFactory::new()->make()
		);

		$data = Reviewfactory::new()->raw();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->update($id, $request);
		});

		$this->assertDatabaseHas('reviews', [
			'id' => $review->id,
			'title' => $data['title'],
			'user_id' => $user->id,
		]);

		$response->assertSee($data['title']);
	}

	/** @test */
	public function it_rejects_updating_a_review_that_does_not_belong_to_the_user()
	{
		$user = UserFactory::new()->create();

		$review = Reviewfactory::new()->create();

		$data = Reviewfactory::new()->raw();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->update($id, $request);
		});

		$this->assertDatabaseMissing('reviews', [
			'id' => $review->id,
			'title' => $data['title']
		]);

		$response->assertStatus(404);
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
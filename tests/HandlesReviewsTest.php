<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Reviews\Review;
use Reviews\HandlesReviews;
use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Database\Factories\productFactory;
use Reviews\Tests\Database\Factories\UserFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Mtvs\EloquentApproval\ApprovalStatuses;

class HandlesReviewsTest extends TestCase
{
	protected $controller;

	public function setup(): void
	{
		parent::setup();

		$this->controller = new class {
			use HandlesReviews;
		};
	}

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
			return $this->controller->create($request);
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
		$user = UserFactory::new()->create();

		$data = Reviewfactory::new()->raw([
			'reviewable_id' => 100,
		]);

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->controller->create($request);
		});

		$this->assertDatabaseMissing('reviews', [
			'title' => $data['title'],
		]);

		$response->assertStatus(422);
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
			return $this->controller->create($request);
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
			return $this->controller->update($id, $request);
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
		$this->expectException(ModelNotFoundException::class);

		$user = UserFactory::new()->create();

		$review = Reviewfactory::new()->create();

		$data = Reviewfactory::new()->raw();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		// $this->assertDatabaseMissing('reviews', [
		// 	'id' => $review->id,
		// 	'title' => $data['title']
		// ]);

		// $response->assertStatus(404);
	}

	/** @test */
	public function it_suspends_reviews_after_an_update_accordingly()
	{
		$user = UserFactory::new()->create();

		$review = $user->reviews()->save(
			ReviewFactory::new()->approved()->make([
				'rating' => 5,
				'recommend' => true,
			])
		);

		$data = array_merge($review->getAttributes(), Arr::only(
			Reviewfactory::new()->raw(), ['title', 'bodt']
		));

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", "PUT", $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		$this->assertDatabaseHas('reviews', [
			'id' => $review->id,
			'approval_status' => ApprovalStatuses::PENDING,
		]);

		$review->refresh();
		$review->approve();

		$data = array_merge($review->getAttributes(), [
			'rating' => 1,
			'recommend' => false,
		]);

		$request = Request::create("/reviews/{$id}", "PUT", $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		$this->assertDatabaseHas('reviews', [
			'id' => $review->id,
			'approval_status' => ApprovalStatuses::APPROVED,
		]);
	}

	/** @test */
	public function it_ignores_updating_the_reviewable_fields_of_reviews()
	{
		$user = UserFactory::new()->create();

		$review = $user->reviews()->save(
			ReviewFactory::new()->make()
		);
		
		$data = Reviewfactory::new()->raw([
			'reviewable_type' => "NewClass",
			'reviewable_id' => 2,
		]);

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		$this->assertDatabaseHas('reviews', [
			'id' => $review->id,
			'reviewable_type' => $review->reviewable_type,
			'reviewable_id' => $review->reviewable_id,
		]);
	}

		/** @test */
	public function it_can_delete_a_review()
	{
		$user = UserFactory::new()->create();

		$review = $user->reviews()->save(
			ReviewFactory::new()->make()
		);

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'DELETE', [], [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->delete($id, $request);
		});

		$this->assertDatabaseMissing('reviews', [
			'id' => $review->id,
		]);

		$response->assertStatus(200);
	}

	/** @test */
	public function it_rejects_deleting_a_review_that_does_not_belong_to_the_user()
	{
		$this->expectException(ModelNotFoundException::class);

		$user = UserFactory::new()->create();

		$review = Reviewfactory::new()->create();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/reviews/{$id}", 'DELETE', [], [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->delete($id, $request);
		}); 

		// $this->assertDatabaseHas('reviews', [
		// 	'id' => $review->id,
		// ]);

		// $response->assertStatus(404);
	}
}

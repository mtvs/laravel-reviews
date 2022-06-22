<?php

namespace Reviews\Tests;

use Illuminate\Http\Request;
use Reviews\HandlesReviews;
use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Database\Factories\ProductFactory;
use Reviews\Tests\Database\Factories\UserFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Mtvs\EloquentApproval\ApprovalStatuses;
use Reviews\Tests\Models\Review;

class HandlesReviewsTest extends TestCase
{
	protected $controller;

	public function setup(): void
	{
		parent::setup();

		$this->controller = new class {
			use HandlesReviews;

			protected function validator(array $data)
			{
				return Validator::make($data, [
					'rating' => ['required', 'numeric', 'min:1', 'max:'.Review::RATING_MAX],
					'title' => ['required', 'string', 'max:255'],
					'body' => ['required', 'string'],
				]);
			}
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
			return $this->controller->store($request);
		});

		$this->assertDatabaseHas('reviews', [
			'title' => $data['title'],
			'user_id' => $user->id,
			'reviewable_type' => $data['reviewable_type'],
			'reviewable_id' => $data['reviewable_id'],
		]);

		$response->assertSee($data['title']);
	}

	/** @test */
	public function it_vaidates_the_input_before_creating_a_review()
	{
		$this->withExceptionHandling();

		$user = UserFactory::new()->create();

		$data = Reviewfactory::new()->raw([
			'rating' => 0,
			'title' => '',
			'body' => '',
		]);

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->controller->store($request);
		});

		$this->assertDatabaseMissing('reviews', [
			'rating' => $data['rating'],
			'title' => $data['title'],
			'body' => $data['body'],
		]);

		$response->assertStatus(422);
	}

	/** @test */
	public function it_rejects_creating_reviews_for_non_existing_reviewables()
	{
		$this->withExceptionHandling();

		$user = UserFactory::new()->create();

		$data = Reviewfactory::new()->raw([
			'reviewable_id' => 100,
		]);

		$this->actingAs($user);

		$request = Request::create('/reviews', 'POST', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) {
			return $this->controller->store($request);
		});

		$this->assertDatabaseMissing('reviews', [
			'title' => $data['title'],
		]);

		$response->assertStatus(422);
	}

	/** @test */
	public function it_rejects_creating_multiple_reviews_from_single_user()
	{
		$this->withExceptionHandling();

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
			return $this->controller->store($request);
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

		$request = Request::create("/review/{$id}", 'PUT', $data, [], [], [
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
	public function it_vaidates_the_input_before_updating_a_review()
	{
		$this->withExceptionHandling();

		$user = UserFactory::new()->create();

		$review = $user->reviews()->save(
			ReviewFactory::new()->make()
		);

		$data = Reviewfactory::new()->raw([
			'rating' => 0,
			'title' => '',
			'body' => '',
		]);

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/review/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		$this->assertDatabaseMissing('reviews', [
			'id' => 1,
			'rating' => $data['rating'],
			'title' => $data['title'],
			'body' => $data['body'],
		]);

		$response->assertStatus(422);
	}

	/** @test */
	public function it_rejects_updating_a_review_that_does_not_belong_to_the_user()
	{
		$this->withExceptionHandling();

		$user = UserFactory::new()->create();

		$review = Reviewfactory::new()->create();

		$data = Reviewfactory::new()->raw();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/review/{$id}", 'PUT', $data, [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->update($id, $request);
		});

		$this->assertDatabaseMissing('reviews', [
			'id' => $review->id,
			'title' => $data['title']
		]);

		$response->assertStatus(404);
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

		$request = Request::create("/review/{$id}", "PUT", $data, [], [], [
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

		$request = Request::create("/review/{$id}", "PUT", $data, [], [], [
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

		$request = Request::create("/review/{$id}", 'PUT', $data, [], [], [
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

		$request = Request::create("/review/{$id}", 'DELETE', [], [], [], [
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
		$this->withExceptionHandling();

		$user = UserFactory::new()->create();

		$review = Reviewfactory::new()->create();

		$this->actingAs($user);

		$id = $review->id;

		$request = Request::create("/review/{$id}", 'DELETE', [], [], [], [
			'HTTP_ACCEPT' => 'application/json',
		]);

		$response = $this->handleRequestUsing($request, function ($request) use ($id) {
			return $this->controller->delete($id, $request);
		}); 

		$this->assertDatabaseHas('reviews', [
			'id' => $review->id,
		]);

		$response->assertStatus(404);
	}
}

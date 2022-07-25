<?php

namespace Mtvs\Reviews\Tests;

use Illuminate\Http\Request;
use Mtvs\Reviews\IndexesReviews;
use Mtvs\Reviews\Tests\Database\Factories\ProductFactory;
use Mtvs\Reviews\Tests\Database\Factories\ReviewFactory;

class IndexesReviewsTest extends TestCase
{
	use IndexesReviews;

	/** @test */
	public function it_can_index_the_reviews()
	{
		$product = ProductFactory::new()->create();

		$product->reviews()->saveMany($reviews = [
			ReviewFactory::new()->approved()->make(),
			ReviewFactory::new()->suspended()->make(),
			ReviewFactory::new()->rejected()->make(),
		]);

		$request = Request::create("/products/{$product->id}/reviews");

		$id = $product->id;

		$response = $this->handleRequestUsing($request, function () use ($id) {
			return $this->index('products', $id);
		});

		$response->assertSee($reviews[0]->title);
		$response->assertDontSee($reviews[1]->title);
		$response->assertDontSee($reviews[2]->title);
	}
}

<?php

namespace Mtvs\Reviews;

class ReviewsRouteMethods
{
	public function reviews()
	{
		return function ($options = []) {
			$namespace = class_exists(
				$this->prependGroupNamespace('ReviewsController')
			) ? null : 'App\Http\Controllers';

			$this->group(['namespace' => $namespace], function () use($options) {
				if ($options['index'] ?? true) {
					$this->get(
						'/reviews',
					 	'ReviewsController@index'
					)->name('reviews.index');
				}

				if ($options['store'] ?? true) {
					$this->post('/reviews', 'ReviewsController@store')
						->name('reviews.store');
				}

				if ($options['update'] ?? true) {
					$this->put('/reviews/{key}', 'ReviewsController@update')
						->name('reviews.update');
				}

				if ($options['destroy'] ?? true) {
					$this->delete('/reviews/{key}', 'ReviewsController@destroy')
						->name('reviews.destroy');
				}
			});
		};
	}
}

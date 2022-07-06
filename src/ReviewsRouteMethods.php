<?php

namespace Reviews;

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
					$this->get('/{type}/{key}/reviews', 'ReviewsController@index')
						->name('reviews.index');
				}

				if ($options['store'] ?? true) {
					$this->post('/reviews', 'ReviewsController@store')
						->name('reviews.store');
				}

				if ($options['update'] ?? true) {
					$this->put('/reviews/{id}', 'ReviewsController@update')
						->name('reviews.update');
				}

				if ($options['destroy'] ?? true) {
					$this->delete('/reviews/{id}', 'ReviewsController@destroy')
						->name('reviews.destroy');
				}
			});
		};
	}
}

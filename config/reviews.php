<?php

return [
	'model' => \App\Models\Review::class,

	'reviewables' => [
		// \App\Models\Product::class,
	],

	// Also change the value in the UI: in the ReviewsStars component
	'rating_max' => 5,
];
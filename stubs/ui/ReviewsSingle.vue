<template>
	<div>
		<div class="mb-1">
			<rating-stars :score="review.rating" />
		</div>
		
		<h4>
			{{ review.title }}
		</h4>

		<div>
			{{ review.body }}
		</div>
	</div>
</template>

<script>
	export default {
		props: [
			'review',
		]
	}
</script>
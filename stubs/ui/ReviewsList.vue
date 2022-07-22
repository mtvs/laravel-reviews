<template>
	<div>
		<div class="list-group mb-2" v-if="reviews.length">
			<div class="list-group-item" v-for="review in reviews">
				<reviews-single :review="review" />
			</div>
		</div>
		
		<div class="alert alert-info" v-else>
			No reviews yet, be the first one to review.
		</div>
	</div>
</template>

<script>
export default {
	props: [
		'reviews'
	]
}
</script>
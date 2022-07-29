<template>
	<div>
		<div class="list-group mb-2" v-if="reviews.length">
			<div class="mb-3">
				<a href="#user-review" class="btn btn-outline-primary">
					Post a review
				</a>
			</div>
			
			<div class="list-group-item" v-for="review in reviews">
				<reviews-single :review="review" />
			</div>
		</div>
		
		<div class="alert alert-info" v-else>
			There are no reviews to be shown yet.
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
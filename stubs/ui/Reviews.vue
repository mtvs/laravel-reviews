<template>
	<div>
		<div class="mb-3" v-if="paginator">
			<reviews-list :reviews="paginator.data"></reviews-list>

			<pagination :data="paginator"
				@page-requested="getPage" />
		</div>

		<div>
			<reviews-current v-if="currentReview"
				:review="currentReview"
				@edit-requested="editRequested"
				@review-deleted="reviewDeleted"/>

			<reviews-form v-else-if="user" 
				:reviewable-type="reviewableType"
				:reviewable-id="reviewableId"
				:review="oldReview"
				@review-created="reviewCreated"
				@review-updated="reviewUpdated"
				@edit-cancelled="editCancelled"/>

			<div class="alert alert-info" v-else>
				Please login to be able to post a review.
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			paginator: null,
			currentReview: this.userReview,
			oldReview: null
		}
	},

	props: [
		'reviewableType',
		'reviewableSet',
		'reviewableId',
		'user',
		'userReview'
	],

	created() {
		this.getPage(`/${this.reviewableSet}/${this.reviewableId}/reviews`)
	},

	methods: {
		getPage(url) {
			axios.get(url).then((response) => {
				this.paginator = response.data
			})
		},

		reviewCreated(data) {
			this.currentReview = data
		},

		editRequested() {
			this.oldReview = this.currentReview

			this.currentReview = null
		},

		editCancelled() {
			this.currentReview = this.oldReview

			this.oldReview = null
		},

		reviewUpdated(data) {
			this.currentReview = data

			this.oldReview = null
		},

		reviewDeleted() {
			this.currentReview = null
		}
	}
}
</script>
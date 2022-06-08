<template>
	<div>
		<div class="mb-3" v-if="paginator">
			<reviews-list :reviews="paginator.data"></reviews-list>

			<pagination :data="paginator"
				@page-requested="getPage" />
		</div>

		<div>
			<reviews-form v-if="! currentReview"
				:reviewable-type="reviewableType"
				:reviewable-id="reviewableId"
				:review="oldReview"
				:auth-check="authCheck"
				@review-created="reviewCreated"
				@review-updated="reviewUpdated"
				@edit-cancelled="editCancelled"/>

			<reviews-current v-else 
				:review="currentReview"
				@edit-requested="editRequested"
				@review-deleted="reviewDeleted"/>
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
		'reviewableSlug',
		'reviewableType',
		'reviewableId',
		'authCheck',
		'userReview'
	],

	created() {
		this.getPage(`/${this.reviewableSlug}/${this.reviewableId}/reviews`)
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
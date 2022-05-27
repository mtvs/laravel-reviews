<template>
	<div class="card">
		<div class="card-body">
			<form>		
				<div class="form-group">
		            <rating-stars :score="form.rating"
		            	@score-selected="(score) => form.rating = score"/>
		        </div>

		        <div class="form-group">
		            <input type="text" class="form-control" 
		            	v-model="form.title"
		            	placeholder="Enter the title."></input>
		        </div>

		        <div class="form-group">
		            <textarea class="form-control" 
		            	v-model="form.body"
		            	placeholder="Write your review."></textarea>
		        </div>


	        	<button type="button" class="btn btn-secondary"
	        		v-if="review" @click="$emit('edit-cancelled')">
	        			Cancel</button>

		        <button type="button" class="btn btn-primary"
		        	@click="review ? update () : create()">Submit</button>
		    </form>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		var form = {
			rating: this.review ? this.review.rating : 0,
			recommend: true,
			title: this.review ? this.review.title : null,
			body: this.review ? this.review.body : null
		}

		return {
			form
		}
	},

	props: [
		'reviewableType',
		'reviewableId',
		'review'
	],

	methods: {
		create() {
			axios.post('/reviews', {
				... this.form,
				'reviewable_type': this.reviewableType,
				'reviewable_id': this.reviewableId,
			}).then(({data}) => {
				this.$emit('review-created', data)
			})
		},

		update() {
			axios.put(`/review/${this.review.id}`, {
				... this.form
			}).then(({data}) => {
				this.$emit('review-updated', data)
			})
		}
	}
}
</script>
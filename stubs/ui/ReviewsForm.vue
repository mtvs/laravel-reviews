<template>
	<div>
		<div class="card">
			<div class="card-body">
				<form @submit.prevent="review ? update () : create(); ">		
					<div class="mb-3">
			            <div :class="{'is-invalid': errors.rating}">
			            	<rating-stars :score="form.rating"
			            	@score-selected="(score) => form.rating = score"/>
			            </div>

		            	<div class="invalid-feedback" v-if="errors.rating">
		            		{{ errors.rating[0] }}
		            	</div>
			        </div>

			        <div class="mb-3">
			            <input type="text" class="form-control"
			            	:class="{'is-invalid': errors.title}" 
			            	v-model="form.title"
			            	placeholder="Enter the title."></input>

		            	<div class="invalid-feedback" v-if="errors.title">
		            		{{ errors.title[0] }}
		            	</div>
			        </div>

			        <div class="mb-3">
			            <textarea class="form-control" 
			            	:class="{'is-invalid': errors.body}" 
			            	v-model="form.body"
			            	placeholder="Write your review."></textarea>

		            	<div class="invalid-feedback" v-if="errors.body">
		            		{{ errors.body[0] }}
		            	</div>
			        </div>

		        	<button type="button" class="btn btn-secondary"
		        		v-if="review" @click="$emit('edit-cancelled')">
		        			Cancel</button>

			        <button type="submit" class="btn btn-primary">
			        	Submit
			        </button>
			    </form>
			</div>
		</div>	
	</div>
</template>

<script>
export default {
	data() {
		var form = {
			rating: this.review ? this.review.rating : null,
			title: this.review ? this.review.title : null,
			body: this.review ? this.review.body : null
		}

		return {
			form,
			errors: {}
		}
	},

	props: [
		'reviewableType',
		'reviewableId',
		'review',
	],

	methods: {
		create() {
			axios.post('/reviews', {
				... this.form,
				'reviewable_type': this.reviewableType,
				'reviewable_id': this.reviewableId,
			}).then(({data}) => {
				this.$emit('review-created', data)
			}).catch((error) => { 
				if (error.response) {
					if (error.response.status == 422) {
						this.errors = error.response.data.errors
					}
				}
			})
		},

		update() {
			axios.put(`/reviews/${this.review.id}`, {
				... this.form
			}).then(({data}) => {
				this.$emit('review-updated', data)
			}).catch((error) => { 
				if (error.response) {
					if (error.response.status == 422) {
						this.errors = error.response.data.errors
					}
				}
			})
		}
	}
}
</script>

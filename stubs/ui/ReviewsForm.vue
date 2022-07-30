<template>
	<div>
		<div class="card">
			<div class="card-body">
				<form @submit.prevent="review ? update() : create(); ">		
					<div class="mb-3">
	            		<label for="reveiew-rating" class="form-label">
		            		Select a rating
		            	</label>

			            <div :class="{'is-invalid': errors.rating}">
			            	<reviews-stars :score="form.rating"
			            	@score-selected="(score) => form.rating = score"/>

			            	<select id="review-rating" 
			            		class="visually-hidden"
			            		v-model="form.rating">
			            		<option v-for="i in 5" :value="i">
			            			{{ i }}
			            		</option>
			            	</select>
			            </div>

		            	<div class="invalid-feedback" v-if="errors.rating">
		            		{{ errors.rating[0] }}
		            	</div>
			        </div>

			        <div class="mb-3">
			        	<label for="review-title" class="form-label">
			        		Add a title
			        	</label>

			            <input type="text" id="review-title" 
			            	class="form-control"
			            	:class="{'is-invalid': errors.title}" 
			            	v-model="form.title"></input>

		            	<div class="invalid-feedback" v-if="errors.title">
		            		{{ errors.title[0] }}
		            	</div>
			        </div>

			        <div class="mb-3">
			        	<label for="review-body" class="form-label">
			        		Write your review
			        	</label>

			            <textarea id="review-body" 
			            	class="form-control" 
			            	:class="{'is-invalid': errors.body}" 
			            	v-model="form.body"></textarea>

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
				this.catchValidationError(error)
			})
		},

		update() {
			axios.put(`/reviews/${this.review.id}`, this.form)
				.then(({data}) => {
					this.$emit('review-updated', data)
				}).catch((error) => { 
					this.catchValidationError(error)
				})
		},

		catchValidationError(error) {
			if (error.response) {
				if (error.response.status == 422) {
					this.errors = error.response.data.errors
				}
			}
		}
	}
}
</script>

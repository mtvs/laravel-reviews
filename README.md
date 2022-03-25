# Laravel Reviews

This packages abstracts the code required to have reviews and ratings for the laravel's
Eloquent models.

It enables the users to review the models and the admin to approve those
reviews to be shown.

It adds the rating values, `rating_average` and `rating_count`, to a model from
its reviews.

It provides the abilty to sort the models to which reviews belong based on their
rating from the reviwes using the Bayesian algorithm.

## Components

### The Review Schema

It specifies the DB table structure.

* id
* rating
* title
* body
* recommend
* user_id
* reviewable_type
* reviewable_id


### The Review Model

It defines the relations to the `User` and `Reviewable` models.

* reviewable()
* user()

### The Reviewable Trait

It defines the relation to the `Review` model. And it loads the rating
aggregations on the model. Also it provides the ability to sort the models
based on their rating by defining a query scope to do that.

* reviews()
* withRating()
* scopeHigestRated()

### The `PerformsReviews` Trait

It defines the necessary methods for the `User` model that perform the reviews.

* reviews()
* hasReviewed($reviewable): bool


### The Reviews View

It lists the reviews of a specefied model. It also provides the UI to the user
to review a product.

### The User's Reviews Controller

It defines the method to handle the CRUD operations to
enable a user to review a model.

* index()
* create()
* update()
* delete()

### The Admin's Reviews View

It lists the reviews for the adminstration. It provides the UI to approve the
reviews.

### The Admin's Reviews Controller

It handles the listing and approval of the reviews for the admin.

* index()
* setApprovalStatus()
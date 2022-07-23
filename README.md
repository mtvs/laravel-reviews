# Laravel Reviews

![laravel-reviews](https://user-images.githubusercontent.com/8286154/180592946-5d5d99ef-9e06-489a-9de9-b7a5184a6637.gif)

Ratings and reviews for the Laravel's Eloquent models.

Users will be able to rate and review the reviewable models. Then these reviews
can be approved and be shown.

You will be able to load the ratings average and count on a reviewable model 
and display them.

It will provide the ability to sort the reviewables based on their
ratings average using the Bayesian algorithm.

## Installation And Setup

```sh

composer require 

```
Then publish the files that are supposed to be in your codebase in order to be
customizable by you. They're the review model, its database migration, its 
database factory, the HTTP controller and the config file. 

```sh

php artisan vendor:publish

```
Next in your routes file, call the following macro and the router to register 
the default routes. You can use `artisan route:list` to see the routes.

```php

Route::reviews()

```
Then, if you want to use the UI components, run the following command to 
install them. They're written using Vue and Bootstrap. Also a stylesheet and a
pack of font icons are installed in the public directory. 

```sh

php artisan reviews:ui

```
Now let's setup the models. There are some traits that are meant to be imported
in the review model, the user model and also the model(s) that are going to be
reviewed. The review model's trait has already been imported in it when it was
installed. But the other traits need to be installed manually.

```php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Reviews\PerformsReviews;

class User extends Authenticatable
{
	use PerformsReviews;
}

```
```php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Reviews\Reviewable;

class Product extends Model
{
    use Reviewable;
}

```
You also have to specify the reviewable models in the reviews config file.

```php
'reviewables' => [
	\App\Models\Product::class,	
],
```
## Usage

### The Ratings Component

To display the average and the count of a reviewable model's ratings, you can
call `<x-ratings>`. 

```html

<x-ratings :average="$product->ratings_avg" :count="$product->ratings_count"/>

```

Do not forget to load those values on the model by calling `loadRatings()` on
it or eager load them when making the query by calling `withRatings()`.

### The Reviews Component

To display the list of the reviews of a reviewable model and also the form to
post them, you can call `<x-reviews>`.

```html

<x-reviews :reviewable="$product"/>

```
It also contains a call to the `<x-ratings>`.

You can link the ratings component that you possibly use in the upper part of
the page to the reviews component by wrapping the ratings in an
`<a href="reviews">` referring the reviews component.

### Ranking Based on The Ratings

Reviewable models can be sorted based on their ratings when they're queried.
To do so call the `highestRated()` on the query. It uses the Bayesian average 
formula to calculate the score of each model and sort them from the highest to
the lowest score.

### The Approval of Reviews

The review model uses `Approvable` trait from 
[mtvs/eloquent-approval](https://github.com/mtvs/eloquent-approval) to enable 
to manage which reviews are allowed to be displayed. 
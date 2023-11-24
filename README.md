# Simple Ecommerce AI Chat Helper

This is a simple ecommerce chat helper bot which allows a user to search for products in a more conversational manner.

A user can ask the bot a question relating to products e.g. 'Find me a cheap black dress in extra small' and with any luck, if such a product exists in the database it should be found and returned to the user.

## Steps to install

Ensure php is installed on your machine, run `./vendor/bin/sail up` on the base directory first to build the docker container.

Run migrations with `sail php artisan migrate` and seed dummy data with `sail php artisan db:seed`.

Build development assets with `npm run dev`.

Away you go on http://localhost/ and have fun asking about product data.

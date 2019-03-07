# Contacts App

## Demo

Try out the demo app at [contacts.plehatron.com](https://contacts.plehatron.com).

REST API documentation can be seen at https://contacts.plehatron.com/api.

## First time setup

Prerequisites:

- have [Docker](https://docs.docker.com/install/) and [Docker Compose](https://docs.docker.com/compose/install/) installed
- run the commands with root permissions

Pull and build required images:

```bash
docker-compose pull
docker-compose build
```

Start the containers in daemon mode:

```bash
docker-compose up -d
```

Install the required PHP packages:

```bash
./docker/bin/php composer install
```

Clear the backend app cache:

```bash
./docker/bin/php bin/console cache:clear
```

Install the required frontend packages:

```bash
./docker/bin/node yarn install
./docker/bin/node yarn encore dev
```

Create the database, run migrations and load the database with fixtures:

```bash
./docker/bin/php bin/console doctrine:database:create
./docker/bin/php bin/console doctrine:migrations:migrate
./docker/bin/php bin/console doctrine:fixtures:load
```

Open the app in your browser at [contacts-app.localhost:8091](http://contacts-app.localhost:8091).

## Software stack

Core:

- Docker 18.09
- Docker Compose 1.24
- PHP 7.3
- MySQL 8.0
- Nginx 1.14
- Node.js 10 LTS

Frameworks and Tools:

- [Symfony 4.2](https://symfony.com/)
- [API Platform 1.2](https://api-platform.com/)
- [PHPUnit 6.5](https://phpunit.de/)
- [PHPStan](https://github.com/phpstan/phpstan)
- [Spectre.css](https://picturepan2.github.io/spectre/index.html)
- [Vue 2.6](https://vuejs.org/)

Additional libraries:

- https://github.com/giggsey/libphonenumber-for-php
- https://github.com/misd-service-development/phone-number-bundle
- https://github.com/dustin10/VichUploaderBundle
- https://github.com/gumlet/php-image-resize

## Docker containers

Docker with Docker Compose is used for running the whole stack. Four containers are used:
- `nginx` - HTTP server for static assets and to proxy requests to PHP-FPM.
- `php` - Runs PHP-FPM in daemon mode for the Symfony app, has PHP CLI enabled for unit/functional testing and 
[Composer](https://getcomposer.org/) for dependency management.
- `db` - MySQL database for persisting contact data.
- `node` - Used by Symfony Encore for managing and compiling frontend assets using Webpack module bundler and Yarn dependency manager.

## Backend

Backend app uses Symfony with API Platform framework to provide an REST API for the frontend part of the application.
API Platform was chosen because of it's numerous benefits, including:
- Easy to extend entities with new properties, changes are propagated automatically.
- [OpenAPI](https://swagger.io/docs/specification/about/) (Swagger) documentation is generated automatically.
- Support for JSON-LD with deprecations that enable 
[evolving APIs instead of API versions](https://api-platform.com/docs/core/deprecations/#deprecating-resources-and-properties-alternative-to-versioning).

Source files of the backend app can be found in the `config/`, `src/`, `templates/`, and `tests/` folders.

## Database and search engine

MySQL database, particularly version 8, is used because of its native fulltext support for InnoDB type tables. 
This was chosen over Elasticsearch server mainly to reduce the memory consumption on the production
VM server, and because the number of users is currently unknown, so a simpler solution and stack is enough for now.

## Tests

Backend is covered with functional tests since most of the backend logic is handled by the API Platform. JSON schema
files are used to validation REST API responses where appropriate.

Run the tests with command:
```bash
docker/bin/php bin/phpunit
```

Code coverage for the backend tests can be found at `./var/log/coverage`.

## Static Analysis

PHPStan is used for finding errors in code. Run it with command:

```
vendor/bin/phpstan analyse -l 5 src tests
```

## Frontend

Frontend app uses Vue and is configured as a single page app, all the "view" logic happens in the frontend. It uses the
API Platform's REST API with JSON-LD data format.

Source files can be found in the `assets/` folder.

## Assumptions

There is no user authentication. It is assumed that there is only one application consumer.

## User Roles

- `User` - a single actor of the application. Someone who uses the application to store and organise its contacts.

## User Stories

**1. As a user I can create or edit a contact, which includes the first name, last name, profile photo, email, 
favourite check, and one or more telephone numbers with labels.**

Notes:

- Uploaded photos are stored in original format and resized to 200x200 pixels.
- Photo upload is limited to types jpg and png, and a max size of 10MB.
- Number of telephone numbers is unlimited.
- Label value for a telephone number can be entered as a free text.
- Give option to cancel creating a new contact.

Confirmations:

* Test that creation/editing passes when at least first and last name is entered, otherwise it fails.
* Test that multiple telephone numbers with or without labels are visible after a contact is saved.
* Test that only valid telephone numbers (E164 format, example +38591234567) can be saved.
* Test that telephone number labels can have no more than 255 characters.
* Test that only valid emails can be saved.
* Test that creation/editing fails when first and last name have more than 255 characters each.
* Test that creation/editing can be canceled.
* Test that any entered text values are properly sanitised and escaped.
* Test that a selected profile photo added to a new contact is visible after save.
* Test that creation/editing fails for the profile photo when invalid file type is selected or the max size is above the 10MB limit.

**2. As a user I can delete a contact from the list view or the edit view.**

Notes:

- Ask user for confirmation when deleting a contact.
- Give option to cancel deletion.

Confirmations:

* Test that delete button is visible on list and edit view.
* Test that confirmation dialog is shown when clicked on delete button.
* Test that contact is actually gone once the deletion is confirmed.

**3. As a user I can check/uncheck a contact as favourite directly from the list view or the detail view.**

Notes:

- Favourite button acts like a checkbox but does not have to look like one.

Confirmations:

* Test that a contact checked as favourite is visible in the list of my favourites.
* Test that contact checked as favourite has the check active on the list view and detail view.
* Test that previously favourite contact can be made not-favourite.

**4. As a user I can view my contacts in a list of all contacts and a list of my favourites.**

Confirmations:

* Test that first name, last name, profile photo, favourite check, edit button and delete button are displayed for each contact.
* Test that by clicking on the favourite check it marks that contact as favourite.
* Test that by clicking on edit button an edit view is displayed for that contact.
* Test that by clicking on delete button a delete confirmation dialog is displayed.
* Test that by clicking on the "My favourites" only those with favourite flag are displayed.
* Test that by clicking on the "All contacts" all contacts are displayed.

**5. As a user I can search my contacts using a search box that matches my query against all text fields of a contact.**

Notes:

- Contacts are filtered in near-realtime as the search query is being entered character by character in the search box.

Confirmations:

* Test that contacts whose partial first name matches the entered keyword in search box is displayed in the list.
* Test that a contact whose partial last name matches the entered keyword in search box is displayed in the list.
* Test that a contact whose partial email matches the entered keyword in search box is displayed in the list.
* Test that a contact whose partial telephone number matches the entered keyword in search box is displayed in the list.
* Test that a contact whose partial telephone number label matches the entered keyword in search box is displayed in the list.
* Test that no contacts are displayed when a keyword that does not have a match is entered in the search box.
* Test that search terms can contact spaces.
* Test that search terms longer than 100 characters are truncated.
* Test that search terms are properly sanitised and escaped when displayed in the search box.
* Test that search works in the same way on list of all contacts and my favourites.

**6. As a user I can access a read-only details view of a contact which includes the first name, last name,
profile photo, email, favourite check, and one or more telephone numbers with labels.**

Notes:

- Add a button for accessing the contacts edit page.

Confirmations:

* Test that all specified fields are displayed on the details view.
* Test that email is clickable and links to `mailto:{email}`.
* Test that telephone numbers are clickable and link to `tel:{number}`.
* Test that really long text is clipped and displayed with an ellipsis. 
* Test that not found contact details display Not found page.
* Test that click on favourite button sets/unsets contact as favourite.
* Test that click on back button displays a list of contacts.
* Test that click on edit button displays contact edit page.

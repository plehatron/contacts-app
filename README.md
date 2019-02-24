# Contacts App

## Software Stack

Stack:

- PHP 7.3
- MySQL 8.0
- Nginx 1.14
- Node.js 10 LTS

Frameworks:

- https://api-platform.com/
- https://symfony.com/
- https://picturepan2.github.io/spectre/index.html
- https://vuejs.org/
- https://phpunit.de/

Libraries:

- https://github.com/giggsey/libphonenumber-for-php
- https://github.com/misd-service-development/phone-number-bundle

Why API Platform for the REST API?

- Easier to extend entities with new properties, changes are propagated automatically.
- OpenAPI documentation out-of-the-box.
- Support for JSON-LD with deprecations (evolving APIs instead of API versions).

## Assumptions

There is no user authentication. It is assumed that there is only one application consumer.

## User Roles

- User - a single actor of the application. Someone who uses the application to store and organise its contacts.

## User Stories

**1.1. As a user I can create or edit a contact, which includes the first name, last name, profile photo, email, 
favorite check, and one or more telephone numbers with labels.**

Notes:

- Uploaded photos are stored in original format and resized to 200x200 pixels.
- Photo upload is limited to types jpg and png, and a max size of 10MB.
- Number of telephone numbers is unlimited.
- Label value for a telephone number can be entered as a free text.
- Give option to cancel creating a new contact.

Confirmations:

- Test that creation/editing passes when at least a first or last name or email is entered, otherwise it fails.
- Test that multiple telephone numbers with or without labels are visible after a contact is saved.
- Test that only valid telephone numbers (E164 format, example +38591234567) can be saved.
- Test that telephone number labels can have no more than 255 characters.
- Test that only valid emails can be saved.
- Test that creation/editing fails when first and last name have more than 255 characters each.
- Test that creation/editing can be canceled.
- Test that any entered text values are properly sanitised.
- Test that a selected profile photo added to a new contact is visible after save.
- Test that creation/editing fails for the profile photo when invalid file type is selected or the max size is above 
the 10MB limit.

**1.2. As a user I can delete a contact from the list view or the edit view.**

Notes:

- Ask user for confirmation when deleting a contact.
- Give option to cancel deletion.

Confirmations:

- Test that delete button is visible on list and edit view.
- Test that confirmation dialog is shown when clicked on delete button.
- Test that contact is actually gone once the deletion is confirmed.

**1.3. As a user I can check/uncheck a contact as favorite directly from the list view, edit view, or the detail view.**

Notes:

- Favorite button acts like a checkbox but does not have to look like one.

Confirmations:

- Test that a contact checked as favorite is visible in the list of my favorites.
- Test that contact checked as favorite has the check active on the list view, edit view, and detail view.
- Test that previously favorite contact can be made not-favorite.

**1.4. As a user I can view my contacts in a list of all contacts and a list of my favourites.**

Confirmations:

- Test that by clicking on the favorite check it marks that contact as favorite.
- Test that by clicking on edit button an edit view is displayed for that contact.
- Test that by clicking on delete button a delete confirmation dialog is displayed.
- Test that first name, last name, profile photo, favourite check, edit button and delete button is displayed for each contact.

**1.5. As a user I can search my contacts using a search box that matches my query against all text fields of a contact.**

Notes:

- Contacts are filtered in near-realtime as the search query is being entered character by character in the search box.

Confirmations:

- Test that contacts whose partial first name matches the entered keyword in search box is displayed in the list.
- Test that a contact whose partial last name matches the entered keyword in search box is displayed in the list.
- Test that a contact whose partial email matches the entered keyword in search box is displayed in the list.
- Test that a contact whose partial telephone number matches the entered keyword in search box is displayed in the list.
- Test that a contact whose partial telephone number label matches the entered keyword in search box is displayed in the list.
- Test that no contacts are displayed when a keyword that does not have a match is entered in the search box.
- Test that search terms can contact spaces.
- Test that search terms longer than 255 characters are truncated.
- Test that search terms are properly sanitised and escaped when displayed in the search box.
- Test that search works in the same way on list of all contacts and my favorites.

**1.6. As a user I can access a read-only details view of a contact which includes the first name, last name,
profile photo, email, favorite check, and one or more telephone numbers with labels.**

Notes:

- Add a button for accessing the contacts edit page.

Confirmations:

- Test that all specified fields are displayed on the details view.
- Test that email is clickable and links to `mailto:{email}`.
- Test that telephone numbers are clickable and link to `tel:{number}`.

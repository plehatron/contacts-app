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

Why API Platform for the REST API?
- Easier to extend entities with new properties, changes are propagated automatically.
- OpenAPI documentation out-of-the-box.
- Support for JSON-LD with deprecations (evolving APIs instead of API versions).

## User Roles

- User
- System

## User Stories

**1.1. As a user I can create a new contact, which includes the first name, last name, profile photo, email, favorite 
check, and one or more telephone numbers with labels.**

Notes:
- Uploaded photos are stored in original format and resized to 200x200 pixels.
- Photo upload is limited to formats: jpg and png, and max size of 5MB.
- Number of telephone numbers is unlimited.
- Label value for a telephone number can be entered as a free text.
- Give option to cancel creating a new contact.

**1.2. As a user I can edit a contact where I can change the first name, last name, profile photo, email, favorite check, 
one or more telephone numbers with labels.**

Notes:
- Give option to cancel editing.

**1.4. As a user I can delete a contact from the list view or the edit view.**

Notes:
- Ask user for confirmation when deleting a contact.
- Give option to cancel deletion.

**1.5. As a user I can check/uncheck a contact as favorite directly from the list view, edit view, or the detail view.**

**1.6. As a user I can view my contacts in a list of all contacts and a list of my favourites.**

Notes:
- For each contact display first name, last name, profile photo, favourite check, edit button and delete button.

**1.7. As a user I can search my contacts using a search box that matches my query against all text fields of a contact.**

Notes:
- Contacts are filtered in realtime as the search query is being entered in the search box.

**1.8. As a user I can access a read-only details view of a contact which includes the first name, last name,
profile photo, email, favorite check, and one or more telephone numbers with labels.**

Notes:
- Add a button for accessing the contacts edit page.

Confirmations:
- Test that email is clickable and links to `mailto:{email}`.
- Test that telephone numbers are clickable and link to `tel:{number}`.

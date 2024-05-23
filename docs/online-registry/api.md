---
layout: default
title: REST API
nav_order: 3
parent: Online registry
---

REST API
========

This documentation covers the RESTful API for a template management system with user authentication and dashboard features.

Base URL: `https://mtrgen.com/api`

Endpoints
---------

### `POST /signup`

Registers a new user.

#### Parameters

-   `username`: **(Required)** A string representing the user's unique username. It should consist of alphanumeric characters and underscores or dashes. Example: "john_doe12".

-   `password`: **(Required)** A string representing the user's password. The password should be kept secure and adhere to the application's password policies, if any.

-   `email`: **(Optional)** A valid email address for the user. This field can be left empty or null if the user does not want to provide an email address. Example: "<john.doe@example.com>".

### `POST /login`

The `login` endpoint is used to authenticate a user and generate an access token for the session.

#### Parameters

-   `username`: **(Required)** A string representing the user's unique username. It should consist of alphanumeric characters and underscores or dashes. Example: "john_doe12".

-   `password`: **(Required)** A string representing the user's password. The password should be kept secure and adhere to the application's password policies, if any.

-   `duration`: **(Required)** An integer representing the number of hours the access token should remain valid. A value of `0` will never expire the session.

#### Response

##### 200 OK
{: .text-green-200}

On successful authentication, the endpoint returns a JSON object containing the following information:

-   `status`: A string indicating the success of the operation, with a value of "success".

-   `message`: A string containing a descriptive message, such as "Access token created."

-   `token`: A string representing the generated access token for the authenticated session.

-   `user`: An object containing the user's details.

##### 400 Bad Request
{: .text-red-200}

-   `status`: A string indicating the failure of the operation, with a value of "error".

-   `message`: `Something went wrong.`

##### 401 Unauthorized
{: .text-red-200}

-   `status`: A string indicating the failure of the operation, with a value of "error".

###### Missing credentials

-   `message`: `Unauthorized access. Use credentials to login.`

###### Wrong credentials

-   `message`: `Invalid credentials.`

### `POST /logout`

Logs out the authenticated user. Requires authentication.

### `GET /templates`

Returns all public templates.

### `GET /templates/{vendor}`

Returns all public templates for the specified vendor.

-   Parameters:
    -   `{vendor}`: The vendor's name.

### `GET /templates/{vendor}/{name}`

Returns the public template with the specified vendor and name.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `GET /templates/{vendor}/{name}/details`

Returns the public template details for the specified vendor and name.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `GET /templates/{vendor}/{name}/get`

Downloads the public template with the specified vendor and name.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `GET /templates/{vendor}/{name}/type`

Returns the type of the template with the specified vendor and name.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `POST /templates`

Saves a new template to the system. Requires authentication.

-   Request body: JSON object containing the contents of the template as a string.

### `POST /templates/publish`

Publishes a template. Requires authentication.

-   Request body: JSON object containing template data and files.

### `POST /templates/convert`

Converts a template file to a generated PHP file.

-   Request body: JSON object containing template data and target format.

### `POST /bundles`

Saves a new bundle of templates to the system. Requires authentication.

-   Request body: JSON object containing bundle data.

### `GET /bundles/{vendor}/{name}/{templateName}/get`

Downloads a template from a bundle with the specified vendor, name, and template name.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The bundle's name.
    -   `{templateName}`: The template's name within the bundle.

### `GET /users`

Returns all users.

### `GET /users/{username}`

Returns the user with the specified username.

-   Parameters:
    -   `{username}`: The user's username.

### `POST /users`

Updates a user's information. Requires authentication.

-   Request body: JSON object containing user data.

### `GET /users/{username}/avatar`

Returns the avatar of the user with the specified username.

-   Parameters:
    -   `{username}`: The user's username.

### `POST /users/avatar`

Sets the avatar for the authenticated user.

-   Request body: JSON object containing avatar data.

### `POST /dashboard`

Returns the logged-in user's information.

### `POST /dashboard/check`

Checks if the user is logged in.

### `POST /dashboard/templates/{vendor}`

Returns the templates of the specified vendor for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.

### `POST /dashboard/templates/{vendor}/{name}`

Returns the template with the specified vendor and name for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `POST /dashboard/templates/{vendor}/{name}/details`

Returns the template details for the specified vendor and name for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `POST /dashboard/templates/{vendor}/{name}/get`

Downloads the template with the specified vendor and name for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `GET /dashboard/templates/{vendor}/{name}/type`

Returns the type of the template with the specified vendor and name for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

### `POST /dashboard/templates/{vendor}/{name}/visibility`

Sets the visibility of the template with the specified vendor and name for the logged-in user.

-   Parameters:
    -   `{vendor}`: The vendor's name.
    -   `{name}`: The template's name.

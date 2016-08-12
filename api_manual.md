FORMAT: 1A

# JK dingo API manual

# Users
User resource representation.

## Verify user credentials and return a token [POST /auth]


+ Request (application/x-www-form-urlencoded)
    + Body

            email=jk@jk.jk&password=password

+ Response 200 (application/json)
    + Body

            {
                "token": "NEW_TOKEN"
            }

## Validate token [GET /validateToken]


+ Parameters
    + token: (string, required) - User authentication token

+ Response 204 (application/json)

## Show current user [GET /users/me]
Get a JSON representation of current user (jwt auth)

+ Parameters
    + token: (string, required) - User authentication token

+ Response 200 (application/json)
    + Body

            {
                "data": {
                    "id": 1,
                    "name": "Janis Kopstals",
                    "email": "jk@jk.jk",
                    "created_at": "2016-08-10 23:35:52",
                    "updated_at": "2016-08-10 23:35:52"
                }
            }

## List all users (paginated) [GET /users/{?page,limit}]
Get a JSON representation of all the users

+ Parameters
    + token: (string, required) - User authentication token
    + page: (string, optional) - The page of results to view.
        + Default: 1
    + limit: (string, optional) - The amount of results per page.
        + Default: 10

+ Response 200 (application/json)
    + Body

            {
                "data": [
                    {
                        "id": 1,
                        "name": "Janis Kopstals",
                        "email": "jk@jk.jk",
                        "created_at": "2016-08-10 23:35:52",
                        "updated_at": "2016-08-10 23:35:52"
                    }
                ],
                "meta": {
                    "pagination": {
                        "total": 51,
                        "count": 10,
                        "per_page": 10,
                        "current_page": 1,
                        "total_pages": 6,
                        "links": {
                            "next": "http:\/\/localhost:8000\/api\/users?page=2"
                        }
                    }
                }
            }

## List user registration rules [GET /users/rules]
Get the validation rules applied to register a new user

+ Response 200 (application/json)
    + Body

            {
                "data": {
                    "name": "string|required|min:2",
                    "email": "email|required|unique",
                    "password": "string|min:8",
                    "password_confirmation": "required|same:password"
                }
            }

## Store a newly created resource in storage. [POST /users]


+ Parameters
    + name: (string, required) - Name of user
    + email: (string, required) - Email
    + password: (string, required) - Password
    + password_confirmation: (string, required) - Password confirmation

+ Request (application/x-www-form-urlencoded)

+ Response 201 (application/json)
    + Body

            {
                "data": {
                    "id": 1,
                    "name": "Janis Kopstals",
                    "email": "jk@jk.jk",
                    "created_at": "2016-08-10 23:35:52",
                    "updated_at": "2016-08-10 23:35:52"
                }
            }

## Show specific user (by id) [GET /users/{id}]
Get a JSON representation of current user (jwt auth)

+ Parameters
    + token: (string, required) - User authentication token
    + id: (integer, required) - User ID

+ Response 200 (application/json)
    + Body

            {
                "data": {
                    "id": 1,
                    "name": "Janis Kopstals",
                    "email": "jk@jk.jk",
                    "created_at": "2016-08-10 23:35:52",
                    "updated_at": "2016-08-10 23:35:52"
                }
            }

## Update user [POST /users/{id}]
Update user fields, return updated resource.

+ Parameters
    + token: (string, required) - User authentication token
    + name: (string, required) - Name of user
    + email: (string, required) - Email
    + password: (string, required) - Password
    + password_confirmation: (string, required) - Password confirmation

+ Request (application/x-www-form-urlencoded)

+ Response 201 (application/json)
    + Body

            {
                "data": {
                    "id": 1,
                    "name": "Janis Kopstals",
                    "email": "jk@jk.jk",
                    "created_at": "2016-08-10 23:35:52",
                    "updated_at": "2016-08-10 23:35:52"
                }
            }

## Delete user [DELETE /users/{id}]
Remove the specified user from storage.

+ Parameters
    + token: (string, required) - User authentication token

+ Response 200 (application/json)
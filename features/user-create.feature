Feature:
    In order to create an account
    As a super administrator i want to create any types of users
    As an administrator i want to create project manager and users
    As project manager i want to create users
    As user i don't want to create anybody
    As an anonymous i can't create user

    Scenario Outline:
        As a super administrator i want to create any types of users
        As an administrator i want to create project manager and users
        Given i am an user of type <selfRole>
        Given i set to [request][content] values:
        """
        {
          "firstName": "sa-sa",
          "lastName": "Edouard",
          "email": "sasa@edouard.com",
          "plainPassword": "test1234",
          "roles": "<givenRole>"
        }
        """
        When i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["<givenRole>"]
        Examples:
             | selfRole        | givenRole            |
             | super admin     | ROLE_SUPER_ADMIN     |
             | super admin     | ROLE_ADMIN           |
             | super admin     | ROLE_PROJECT_MANAGER |
             | super admin     | ROLE_USER            |
             | admin           | ROLE_PROJECT_MANAGER |
             | admin           | ROLE_USER            |

    Scenario Outline: As an administrator i can't create an admin / super admin account
        Given i am an user of type admin
        Given i set to [request][content] values:
        """
        {
          "firstName": "a-a",
          "lastName": "Edouard",
          "email": "a-a@edouard.com",
          "plainPassword": "test1234",
          "roles": "<role>"
        }
        """
        When i send a put on route api_users_create
        Then the status code should be 400
        And the response item [errors][children][roles][errors] should not be empty
        Examples:
            | role             |
            | ROLE_SUPER_ADMIN |
            | ROLE_ADMIN       |

    Scenario: As a project manager i can't create a project manager
        Given i am an user of type project manager
        Given i set to [request][content] values:
        """
        {
          "firstName": "pm-pm",
          "lastName": "Edouard",
          "email": "pm-pm@edouard.com",
          "plainPassword": "test1234",
          "roles": "ROLE_PROJECT_MANAGER"
        }
        """
        When i send a put on route api_users_create
        Then the status code should be 400
        And the response item [errors][errors] should not be empty

    Scenario: As a project manager i can create an user
        Given i am an user of type project manager
        Given i set to [request][content] values:
        """
        {
          "firstName": "pm-u",
          "lastName": "Edouard",
          "email": "pm-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        When i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["ROLE_USER"]

    Scenario: As an user i can't create an user
        Given i am an user of type user
        Given i set to [request][content] values:
        """
        {
          "firstName": "u-u",
          "lastName": "Edouard",
          "email": "u-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        When i send a put on route api_users_create
        Then the status code should be 403

    Scenario: As an anonymous i can't create user
        Given i set to [request][content] value {}
        When i send a put on route api_users_create
        Then the status code should be 401
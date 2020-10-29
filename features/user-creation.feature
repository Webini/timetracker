Feature:
    In order to create account
    As a super administrator i want to create any types of users
    As an administrator i want to create project manager and users
    As project manager i want to create users
    As user i don't want to create anybody

    Scenario: As a super administrator i can create a super admin account
        Given i am an user of type super admin
        When i set to [request][content] values:
        """
        {
          "firstName": "sa-sa",
          "lastName": "Edouard",
          "email": "sasa@edouard.com",
          "plainPassword": "test1234",
          "superAdmin": true
        }
        """
        And i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["ROLE_USER", "ROLE_PROJECT_MANAGER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"]

    Scenario: As a super administrator i can create an admin account
        Given i am an user of type super admin
        When i set to [request][content] values:
        """
        {
          "firstName": "sa-a",
          "lastName": "Edouard",
          "email": "sa-a@edouard.com",
          "plainPassword": "test1234",
          "admin": true
        }
        """
        And i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["ROLE_USER", "ROLE_PROJECT_MANAGER", "ROLE_ADMIN"]

    Scenario: As an administrator i can't create an admin account
        Given i am an user of type admin
        When i set to [request][content] values:
        """
        {
          "firstName": "a-a",
          "lastName": "Edouard",
          "email": "a-a@edouard.com",
          "plainPassword": "test1234",
          "admin": true
        }
        """
        And i send a put on route api_users_create
        Then the status code should be 400

    Scenario: As an administrator i can create a project manager
        Given i am an user of type admin
        When i set to [request][content] values:
        """
        {
          "firstName": "a-pm",
          "lastName": "Edouard",
          "email": "a-pm@edouard.com",
          "plainPassword": "test1234",
          "projectManager": true
        }
        """
        And i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["ROLE_USER", "ROLE_PROJECT_MANAGER"]

    Scenario: As a project manager i can't create a project manager
        Given i am an user of type project manager
        When i set to [request][content] values:
        """
        {
          "firstName": "pm-pm",
          "lastName": "Edouard",
          "email": "pm-pm@edouard.com",
          "plainPassword": "test1234",
          "projectManager": true
        }
        """
        And i send a put on route api_users_create
        Then the status code should be 400

    Scenario: As a project manager i can create an user
        Given i am an user of type project manager
        When i set to [request][content] values:
        """
        {
          "firstName": "pm-u",
          "lastName": "Edouard",
          "email": "pm-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a put on route api_users_create
        Then the response should be successful
        And the response item [roles] should be equal to ["ROLE_USER"]

    Scenario: As an user i can't create an user
        Given i am an user of type user
        When i set to [request][content] values:
        """
        {
          "firstName": "u-u",
          "lastName": "Edouard",
          "email": "u-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a put on route api_users_create
        Then the status code should be 403

    Scenario: As an anonymous i can't create user
        When i set to [request][content] value {}
        And i send a put on route api_users_create
        Then the status code should be 401
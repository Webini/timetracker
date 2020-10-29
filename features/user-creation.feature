Feature:
    In order to prove the user API is working

    Scenario: As a super administrator i can create a super admin account
        Given i am an user of type super admin
        When i add to content values:
        """json
        {
          "firstName": "sa-sa",
          "lastName": "Edouard",
          "email": "sasa@edouard.com",
          "plainPassword": "test1234",
          "superAdmin": true
        }
        """
        And i send a post on route api_users_create
        Then the response should be successful
        And the response item [roles] should be ["ROLE_USER", "ROLE_PROJECT_MANAGER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"]

    Scenario: As a super administrator i can create an admin account
        Given i am an user of type super admin
        When i add to content values:
        """json
        {
          "firstName": "sa-a",
          "lastName": "Edouard",
          "email": "sa-a@edouard.com",
          "plainPassword": "test1234",
          "admin": true
        }
        """
        And i send a post on route api_users_create
        Then the response should be successful
        And the response item [roles] should be ["ROLE_USER", "ROLE_PROJECT_MANAGER", "ROLE_ADMIN"]

    Scenario: As an administrator i can't create an admin account
        Given i am an user of type admin
        When i add to content values:
        """json
        {
          "firstName": "a-sa",
          "lastName": "Edouard",
          "email": "a-sa@edouard.com",
          "plainPassword": "test1234",
          "admin": true
        }
        """
        And i send a post on route api_users_create
        Then the response should not be successful

    Scenario: As an administrator i can create a project manager
        Given i am an user of type admin
        When i add to content values:
        """json
        {
          "firstName": "a-pm",
          "lastName": "Edouard",
          "email": "a-pm@edouard.com",
          "plainPassword": "test1234",
          "projectManager": true
        }
        """
        And i send a post on route api_users_create
        Then the response should be successful
        And the response item [roles] should be ["ROLE_USER", "ROLE_PROJECT_MANAGER"]

    Scenario: As a project manager i can't create a project manager
        Given i am an user of type project manager
        When i add to content values:
        """json
        {
          "firstName": "pm-pm",
          "lastName": "Edouard",
          "email": "pm-pm@edouard.com",
          "plainPassword": "test1234",
          "projectManager": true
        }
        """
        And i send a post on route api_users_create
        Then the response should not be successful

    Scenario: As a project manager i can create an user
        Given i am an user of type project manager
        When i add to content values:
        """json
        {
          "firstName": "pm-u",
          "lastName": "Edouard",
          "email": "pm-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a post on route api_users_create
        Then the response should be successful
        And the response item [roles] should be ["ROLE_USER"]

    Scenario: As an user i can't create an user
        Given i am an user of type user
        When i add to content values:
        """json
        {
          "firstName": "u-u",
          "lastName": "Edouard",
          "email": "u-u@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a post on route api_users_create
        Then the response should not be successful
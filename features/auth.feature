# This file contains a user story for demonstration only.
# Learn how to get started with Behat and BDD on Behat's website:
# http://behat.org/en/latest/quick_start.html

Feature:
    In order to prove the connection mechanism is working
    As a non logged user i want to create an account
    As a non logged user i want to login with my account
    As a logged user i want to refresh my token


    Scenario: User will try to connect with correct password
        When i set to [request][content][email] value "user@fixture.fr"
        And i set to [request][content][password] value "demopassword"
        And i send a post on route api_auth_login
        Then the response should be successful

    Scenario: User will try to connect with incorrect password
        When i set to [request][content][email] value "user@fixture.fr"
        And i set to [request][content][password] value "invalid"
        And i send a post on route api_auth_login
        Then the response should not be successful

    Scenario: User will try to refresh his token
        Given i am an user of type user
        And i set my jwt value to [request][content][token]
        And i set my refresh token value to [request][content][refreshToken]
        And i send a post on route api_auth_refresh_token
        Then the response should be successful

    Scenario: User will try to refresh his token without old tokens
        Given i am an user of type user
        And i send a post on route api_auth_refresh_token
        Then the status code should be 401

    Scenario: As an anonymous user i can create an account
        When i set to [request][content] values:
        """
        {
          "firstName": "Anonymous",
          "lastName": "Edouard",
          "email": "Anonymous@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a put on route api_auth_register
        Then the response should be successful
        And the response should have keys [token],[refreshToken],[id],[email],[emailValidated],[firstName],[lastName],[roles]
        And the response should not have keys [password],[plainPassword]

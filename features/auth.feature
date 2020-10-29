# This file contains a user story for demonstration only.
# Learn how to get started with Behat and BDD on Behat's website:
# http://behat.org/en/latest/quick_start.html

Feature:
    In order to prove the connection mechanism is working

    Scenario: User will try to connect with correct password
        When i add to content a key email with value user@fixture.fr
        And i add to content a key password with value demopassword
        When i send a post on route api_auth_login
        Then the response should be successful

    Scenario: User will try to connect with incorrect password
        When i add to content a key email with value user@fixture.fr
        And i add to content a key password with value invalid
        When i send a post on route api_auth_login
        Then the response should not be successful

    Scenario: User will try to refresh his token
        Given i am an user of type user
        And i add to content a key token with my jwt value
        And i add to content a key refreshToken with my refresh token value
        When i send a post on route api_auth_refresh_token
        Then the response should be successful

    Scenario: User will try to refresh his token without old tokens
        Given i am an user of type user
        When i send a post on route api_auth_refresh_token
        Then the status code should be 401

    Scenario: As an anonymous user i can create an account
        When i add to content values:
        """
        {
          "firstName": "Anonymous",
          "lastName": "Edouard",
          "email": "Anonymous@edouard.com",
          "plainPassword": "test1234"
        }
        """
        And i send a post on route api_auth_register
        Then the response should be successful
        And the response should have keys [token],[refreshToken],[id],[email],[emailValidated],[firstName],[lastName],[roles]
        And the response should not have keys [password],[plainPassword]

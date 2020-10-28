Feature:
    In order to prove the user API is working

    Scenario: As an anonymous user i can create an account
        When i add to content values:
        """
        {
          "firstName": "Jean",
          "lastName": "Edouard",
          "email": "jean@edouard.com",
          "plainPassword": "test1234"
        }
        """
        When i send a post on route api_users_register
        Then the response should be successful
        Then the response should have keys [token],[refreshToken],[id],[email],[emailValidated],[firstName],[lastName],[roles]
        Then the response should not have keys [password],[plainPassword]
Feature:
  In order to retrieve single user data
  As any logged users i want to retrieve minimal information of everyone
  As project managers, administrator and super administrator i want
  to retrieve full information of everyone
  As myself, i want to retrieve full information about me

  Scenario: As a super administrator i can see the full information of anyone
    Given i am an user of type super admin
    Given an user of type super admin saved in [users][sa]
    And i set to [route][params][user] the value of [users][sa].id
    And i send a get on route api_users_get_one
    Then the status code should be 200
    And the response should have keys [phoneNumber],[email],[emailValidated]

  Scenario: As an administrator i can see the full information of anyone
    Given i am an user of type admin
    Given an user of type super admin saved in [users][a]
    And i set to [route][params][user] the value of [users][a].id
    And i send a get on route api_users_get_one
    Then the status code should be 200
    And the response should have keys [phoneNumber],[email],[emailValidated]

  Scenario: As a project manager i can see the full information of anyone
    Given i am an user of type project manager
    Given an user of type project manager saved in [users][pa]
    And i set to [route][params][user] the value of [users][pa].id
    And i send a get on route api_users_get_one
    Then the status code should be 200
    And the response should have keys [phoneNumber],[email],[emailValidated]

  Scenario: As an user i can't see full information of other users
    Given i am an user of type user
    Given an user of type user saved in [users][u]
    And i set to [route][params][user] the value of [users][u].id
    And i send a get on route api_users_get_one
    Then the status code should be 200
    And the response should not have keys [phoneNumber],[email],[emailValidated]

  Scenario: As an anonymous i can't get user information
    Given an user of type user saved in [users][u]
    And i set to [route][params][user] the value of [users][u].id
    When i send a get on route api_users_get_one
    Then the status code should be 401
Feature:
  In order to search users on the platform
  As any logged user i want to be able to search user with a keywords
  in email and first name / last name field, i also want to have a pagination
  to avoid retrieving big bunch of data
  As an anonymous i can't search

  Scenario: As an admin i'll search one user by email
    Given i am an user of type admin
    Given an user of type user saved in [user]
    Given i set to [request][parameters][search] the value of [user].email
    When i send a get on route api_users_search
    Then the response should be successful
    And the response item [data] should not be empty
    And the response item [data][0][email] should not be empty
    And the response item [pagination][totalCount] should be greater than 0

  Scenario: As an user i'll search all users
    Given i am an user of type user
    Given an user of type user saved in [trash]
    When i send a get on route api_users_search
    Then the response should be successful
    And the response item [data] should not be empty
    And the response item [pagination][totalCount] should be greater than 1

  Scenario: As an anonymous i can't search
    When i send a get on route api_users_search
    Then the status code should be 401
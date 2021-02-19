Feature:
  In order to search users on the platform
  As any logged user i want to be able to search user with a keywords
    in email and first name / last name field, i also want to have a pagination
    to avoid retrieving big bunch of data
  As any logged user i want to be able to search users not in specific project
  As an anonymous i can't search

  Scenario:
  As any logged user i want to be able to search users not in specific project
    Given i am an user of type user
    Given an user of type project manager saved in [userInProject]
    Given a project named "Test project" created by [userInProject] saved in [project]
    Given i set to [request][parameters][notInProject] the value of [project].id
    When i send a get on route api_users_search
    Then the response should be successful
    And the response item [data] should not be empty
    # total should be equal to 1 because, 1 user is me (not assigned) and 1 is userInproject and assigned
    And the response item [pagination][totalCount] should be equal to 1
    And the response item [data][0][id] should be equal to data in [me].id

  Scenario Outline:
    As any logged user i want to be able to search user with a keywords
    in email and first name / last name field, i also want to have a pagination
    Given i am an user of type <role>
    Given an user of type user saved in [user]
    Given i set to [request][parameters][search] the value of [user].<searchKey>
    When i send a get on route api_users_search
    Then the response should be successful
    And the response item [data] should not be empty
    And the response item [data][0][<searchKey>] should be equal to data in [user].<searchKey>
    And the response item [pagination][totalCount] should be greater than 0
    Examples:
      | role  | searchKey |
      | admin | email     |
      | user  | lastName  |
      | user  | firstName |

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

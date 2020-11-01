Feature:
  In order to get all project's users
  As an admin / super admin i can retrieve all project's users
  As a project manager i can retrieve all project's users if i'm admin on this project
  As an user i can't retrieve project's users
  As an anonymous i can't do anything
  Each assigned users in response should contain user's information, permission, and
  it should not contains project's information

  Scenario: As a super admin i can retrieve all project's users
    Given i am an user of type super admin
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 200
    Then the response should count 2

  Scenario: As an admin i can retrieve all project's users
    Given i am an user of type admin
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 200
    Then the response should count 2

  Scenario: As a project manager i can retrieve all project's users if i'm admin on this project
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given i create a project named "Test get all" saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 200
    Then the response should count 2

  Scenario: As a project manager i can't retrieve all project's users if i'm not admin on this project
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 403

  Scenario: As an user i can't retrieve project's users
    Given i am an user of type user
    Given a project saved in [project]
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 403

  Scenario: As an anonymous i can't do
    Given i set to [route][params][project] value 1
    When i send a get on route api_projects_users_get_all
    Then the status code should be 401

  Scenario: Each assigned users in response should contain user's information,
  permission, and it should not contains project's information
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given i create a project named "Test get all" saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_users_get_all
    Then the status code should be 200
    Then the response item [0][assigned][firstName] should not be empty
    Then the response item [1][assigned][firstName] should not be empty
    Then the response item [0][permissions] should not be empty
    Then the response item [1][permissions] should not be empty
    Then the response item [0][project] should not be present
    Then the response item [1][project] should not be present
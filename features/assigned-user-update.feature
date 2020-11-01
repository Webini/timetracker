Feature:
  In order to edit an assigned user
  As an admin / super admin i can edit everybody
  As a project manager i can edit users from projects where i'm admin
  As an user i can't edit anyone
  As an anonymous i can't do anything

  Scenario: As a super admin i can edit everybody
    Given i am an user of type super admin
    Given an user of type user saved in [user]
    Given a project saved in [project]
    Given i assign user [user] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [user].id
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 200
    And the response item [permissions] should be equal to 1
    And the response item [assigned] should not be empty

  Scenario: As an admin i can edit everybody
    Given i am an user of type admin
    Given an user of type user saved in [user]
    Given a project saved in [project]
    Given i assign user [user] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [user].id
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 200
    And the response item [permissions] should be equal to 1
    And the response item [assigned] should not be empty

  Scenario: As a project manager i can edit users from projects where i'm admin
    Given i am an user of type project manager
    Given an user of type user saved in [user]
    Given i create a project named "Test PM" saved in [project]
    Given i assign user [user] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [user].id
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 200
    And the response item [permissions] should be equal to 1
    And the response item [assigned] should not be empty

  Scenario: As a project manager i can't edit users from projects where i'm not admin
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 403

  Scenario: As an user i can't edit anyone
    Given i am an user of type user
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 403

  Scenario: As an anonymous i can't do anything
    Given i set to [route][params][project] value 1
    Given i set to [route][params][user] value 1
    Given i set to [request][content][permissions] value 1
    When i send a patch on route api_projects_users_update
    Then the status code should be 401

Feature:
  In order to remove an user from a project
  As an admin / super admin i can delete anyone
  As a project manager i can only delete users from project where i'm admin
  As an user i can't delete users from any project
  As an anonymous i can't do anything

  Scenario: As a super admin i can delete anyone
    Given i am an user of type super admin
    Given an user of type user saved in [users][assigned]
    Given i create a project named "Test SA" saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    When i send a delete on route api_projects_users_delete
    Then the status code should be 204

  Scenario: As an admin i can delete anyone
    Given i am an user of type admin
    Given an user of type user saved in [users][assigned]
    Given i create a project named "Test admin" saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    When i send a delete on route api_projects_users_delete
    Then the status code should be 204

  Scenario: As a project manager i can only delete users from project where i'm admin
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given i create a project named "Test PM" saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    When i send a delete on route api_projects_users_delete
    Then the status code should be 204

  Scenario: As a project manager i can't delete users from project where i'm not admin
    Given i am an user of type project manager
    Given an user of type user saved in [users][assigned]
    Given an user of type project manager saved in [users][pm]
    Given a project named "Test nop" created by [users][pm] saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    When i send a delete on route api_projects_users_delete
    Then the status code should be 403

  Scenario: As an user i can't delete users from any project
    Given i am an user of type user
    Given an user of type user saved in [users][assigned]
    Given an user of type project manager saved in [users][pm]
    Given a project named "Test nop" created by [users][pm] saved in [project]
    Given i assign user [users][assigned] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    When i send a delete on route api_projects_users_delete
    Then the status code should be 403

  Scenario: As an anonymous i can't do anything
    Given i set to [route][params][project] value 1
    Given i set to [route][params][user] value 1
    When i send a delete on route api_projects_users_delete
    Then the status code should be 401

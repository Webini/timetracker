Feature:
  In order to create a new assigned users to project
  As an administrator / super admin i could assign any users to any projects with any permission
  As a project manager i can only assign users to projects where i'm admin
  As an user i can't assign anybody to any projects
  As an anonymous i can't do anything

  Background:
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given i set to [request][content][assigned] the value of [users][assigned].id
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline: As an admin / super admin i could assign any users to any projects with any permission
    Given i am an user of type <userType>
    Given i set to [request][content][permissions] value 7
    When i send a put on route api_projects_users_create
    Then the status code should be 201
    Examples:
      | userType    |
      | super admin |
      | admin       |

  Scenario: As a project manager i can only assign users to projects where i'm assigned
    Given i am an user of type project manager
    Given i create a project named "Test PM" saved in [project]
    Given i set to [request][content][permissions] value 7
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_users_create
    Then the status code should be 201

  Scenario: As a project manager i can't assign users to projects where i'm not assigned
    Given i am an user of type project manager
    Given i set to [request][content][permissions] value 7
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_users_create
    Then the status code should be 403

  Scenario: As an user i can't assign anybody to any projects
    Given i am an user of type user
    Given i set to [request][content][permissions] value 7
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_users_create
    Then the status code should be 403

  Scenario: As an anonymous i can't do anything
    Given i set to [request][content][permissions] value 7
    When i send a put on route api_projects_users_create
    Then the status code should be 401

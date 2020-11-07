Feature:
  In order to edit an assigned user
  As an admin / super admin i can edit everybody
  As a project manager i can edit users from projects where i'm assigned
  As an user i can't edit anyone
  As an anonymous i can't do anything

  Background:
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given an user [users][assigned] assigned to project [project] with permission cud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id
    Given i set to [request][content][permissions] value 1

  Scenario Outline: As an admin / super admin i can edit everybody
    Given i am an user of type <role>
    When i send a patch on route api_projects_users_update
    Then the status code should be 200
    And the response item [permissions] should be equal to 1
    And the response item [assigned] should not be empty
    Examples:
      | role        |
      | super admin |
      | admin       |

  Scenario: As a project manager i can edit users from projects where i'm assigned
    Given i am an user of type project manager
    Given an user [me] assigned to project [project] with permission none
    When i send a patch on route api_projects_users_update
    Then the status code should be 200
    And the response item [permissions] should be equal to 1
    And the response item [assigned] should not be empty

  Scenario: As a project manager i can't edit users from projects where i'm not assigned
    Given i am an user of type project manager
    When i send a patch on route api_projects_users_update
    Then the status code should be 403

  Scenario: As an user i can't edit anyone
    Given i am an user of type user
    When i send a patch on route api_projects_users_update
    Then the status code should be 403

  Scenario: As an anonymous i can't do anything
    When i send a patch on route api_projects_users_update
    Then the status code should be 401

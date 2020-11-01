Feature:
  In order to remove an user from a project
  As an admin / super admin i can delete anyone
  As a project manager i can only delete users from project where i'm admin
  As an user i can't delete users from any project
  As an anonymous i can't do anything

  Background:
    Given an user of type user saved in [users][assigned]
    Given a project saved in [project]
    Given an user [users][assigned] assigned to project [project] with permission cud
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][user] the value of [users][assigned].id

  Scenario Outline: As an admin / super admin i can delete anyone
    Given i am an user of type <userType>
    When i send a delete on route api_projects_users_delete
    Then the status code should be 204
    Examples:
      | userType    |
      | super admin |
      | admin       |

  Scenario Outline:
    As a project manager i can only delete users from project where i'm admin
    As a project manager i can't delete users from project where i'm not admin
    As an user i can't delete users from any project
    Given i am an user of type <userType>
    Given an user <assigned> assigned to project [project] with permission <permission>
    When i send a delete on route api_projects_users_delete
    Then the status code should be <expectedStatus>
    Examples:
     | userType        | assigned | permission | expectedStatus |
     | project manager | [me]     | admin      | 204            |
     | user            | [me]     | cud       | 403            |
     | project manager | [me]     | cud       | 403            |

  Scenario: As an anonymous i can't do anything
    When i send a delete on route api_projects_users_delete
    Then the status code should be 401

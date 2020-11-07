Feature:
  In order to get project's user
  As an admin / super admin i can retrieve all users
  As a project manager i can retrieve project's users if i'm assigned to this project
  As a project manager i can't retrieve project's users if i'm not assigned to this project
  As an user i can retrieve myself
  As an anonymous i can't do anything

  Background:
    Given a project saved in [project]
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline:
    In order to get project's user
    As an admin / super admin i can retrieve all users
    As a project manager i can retrieve all project's users if i'm assigned to this project
    As an user i can retrieve myself
    Given i am an user of type <myRole>
    Given an user of type user saved in [fakedUser]
    Given an user [me] assigned to project [project] with permission none
    Given an user [fakedUser] assigned to project [project] with permission cud
    Given i set to [route][params][user] the value of <testedUser>.id
    When i send a get on route api_projects_users_get
    Then the status code should be <expectedStatus>
    Examples:
        | myRole          | testedUser  | expectedStatus |
        | super admin     | [fakedUser] | 200            |
        | admin           | [fakedUser] | 200            |
        | project manager | [fakedUser] | 200            |
        | user            | [me]        | 200            |
        | user            | [fakedUser] | 403            |

  Scenario Outline:
    As a project manager i can't retrieve project's users if i'm not assigned to this project
    Given i am an user of type <myRole>
    Given an user of type user saved in [fakedUser]
    Given an user [fakedUser] assigned to project [project] with permission cud
    Given i set to [route][params][user] the value of <testedUser>.id
    When i send a get on route api_projects_users_get
    Then the status code should be <expectedStatus>
    Examples:
       | myRole          | testedUser  | expectedStatus |
       | super admin     | [fakedUser] | 200            |
       | admin           | [fakedUser] | 200            |
       | admin           | [me]        | 404            |
       | project manager | [fakedUser] | 403            |
       | user            | [me]        | 404            |
       | user            | [fakedUser] | 403            |


  Scenario: As an anonymous i can't do anything
    Given i set to [route][params][user] value 1
    When i send a get on route api_projects_users_get
    Then the status code should be 401



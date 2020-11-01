Feature:
  In order to retrieve task data
  As an admin / super admin i can retrieve all tasks
  As a project manager / user i can retrieve only tasks from projects i'm assigned
  As anon i can't do anything

  Background:
    Given an user of type project manager saved in [fakeUser]
    Given a project saved in [project]
    Given a new task created for project [project] saved in [task]
    Given i set to [route][params][task] the value of [task].id
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline:
    As an admin / super admin i can retrieve all tasks
    As a project manager / user i can retrieve only tasks from projects i'm assigned
    Given i am an user of type <role>
    Given an user <assignedUser> assigned to project [project] with permission <assignedPerm>
    When i send a get on route api_projects_tasks_get_one
    Then the status code should be <expectedStatus>
    Examples:
        | role            | assignedUser | assignedPerm | expectedStatus |
        | super admin     | [fakeUser]   | admin        | 200            |
        | super admin     | [me]         | cud         | 200            |
        | admin           | [fakeUser]   | admin        | 200            |
        | admin           | [me]         | cud         | 200            |
        | project manager | [fakeUser]   | admin        | 403            |
        | project manager | [me]         | admin        | 200            |
        | user            | [fakeUser]   | admin        | 403            |
        | user            | [me]         | cud         | 200            |

  Scenario: As anon i can't do anything
    When i send a get on route api_projects_tasks_get_one
    Then the status code should be 401
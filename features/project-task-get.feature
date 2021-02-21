Feature:
  In order to retrieve task data
  As an admin / super admin i can retrieve all tasks
  As a project manager / user i can retrieve only tasks from projects i'm assigned
  As anon i can't do anything
  As an admin / super admin / project manager i want to retrieve total time spent by all users on tasks
  As an user i want to retrieve only my total time spent

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
         | super admin     | [fakeUser]   | none         | 200            |
         | super admin     | [me]         | cud          | 200            |
         | admin           | [fakeUser]   | none         | 200            |
         | admin           | [me]         | cud          | 200            |
         | project manager | [fakeUser]   | none         | 403            |
         | project manager | [me]         | none         | 200            |
         | user            | [fakeUser]   | none         | 403            |
         | user            | [me]         | cud          | 200            |


  Scenario: As anon i can't do anything
    When i send a get on route api_projects_tasks_get_one
    Then the status code should be 401

  Scenario Outline:
    As an admin / super admin / project manager i want to retrieve total time spent by all users on tasks
    As an user i want to retrieve only my total time spent
    Given i am an user of type <role>
    Given an user [me] assigned to project [project] with permission cud
    Given an user [fakeUser] assigned to project [project] with permission cud
    Given a timer started at 2020-10-10 00:00:00 during 1h 0m for task [task] and user [fakeUser]
    Given a timer started at 2020-10-18 21:00:00 during 0h 42m for task [task] and user [me]
    Given a timer started at 2020-10-17 00:00:00 during 2h 0m for task [task] and user [me]
    Given i have a running timer for task [task]
    When i send a get on route api_projects_tasks_get_one
    Then the status code should be 200
    Then the response item [timers] should not be empty
    Then the response item [timers][0][owner] should be equal to data in [me].id
    Then the response item [timers][0][duration] should be equal to 9720
    Then the response item [timers][0][runningTimer] should be equal to true
    Then the response item [timers] should count <entries>
    Examples:
       | role            | entries |
       | project manager | 2       |
       | admin           | 2       |
       | super admin     | 2       |
       | user            | 1       |


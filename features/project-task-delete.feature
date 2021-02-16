Feature:
  In order to delete an existing task
  As an admin / super admin i can delete tasks for all projects
  As a project manager / user i can delete tasks when i have the delete permission
  As an PM / user without perm i can't delete tasks
  As an anonymous i can't do anything

  Background:
    Given a project saved in [project]
    Given a new task created for project [project] saved in [task]
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][task] the value of [task].id

  Scenario Outline:
    As an admin / super admin i can delete tasks for all projects
    As a project manager / user i can delete tasks when i have the delete permission
    As an anonymous i can't do anything
    Given i am an user of type <role>
    Given an user [me] assigned to project [project] with permission <permission>
    When i send a delete on route api_projects_tasks_delete
    Then the status code should be <statusCode>
    Examples:
         | role            | permission  | statusCode |
         | admin           | none        | 204        |
         | super admin     | none        | 204        |
         | project manager | none        | 403        |
         | project manager | delete task | 204        |
         | user            | none        | 403        |
         | user            | delete task | 204        |

  Scenario: As an anonymous i can't do anything
    When i send a delete on route api_projects_tasks_delete
    Then the status code should be 401

Feature:
  In order to edit an existing task
  As an admin / super admin i can edit tasks for all projects
  As a project manager / user i can edit tasks when i have the update permission
  As an anonymous i can't do anything

  Background:
    Given a project saved in [project]
    Given a new task created for project [project] saved in [task]
    Given i set to [route][params][project] the value of [project].id
    Given i set to [route][params][task] the value of [task].id

  Scenario Outline: As an admin / super admin i can edit tasks for all projects
    Given i am an user of type <userType>
    Given i set to [request][content][name] value "<projectName>"
    When i send a patch on route api_projects_tasks_update
    Then the status code should be 200
    Then the response item [name] should be equal to "<projectName>"
    Examples:
       | userType    | projectName      |
       | admin       | Test admin       |
       | super admin | Test super admin |

  Scenario Outline: As a project manager / user i can edit tasks when i have the update permission
    Given i am an user of type <userType>
    Given an user [me] assigned to project [project] with permission <permission>
    Given i set to [request][content][name] value "Test project"
    When i send a patch on route api_projects_tasks_update
    Then the status code should be <statusCode>
    Examples:
       | userType        | permission  | statusCode |
       | project manager | update task | 200        |
       | user            | update task | 200        |
       | project manager | read task   | 403        |
       | user            | read task   | 403        |

  Scenario: As an anonymous i can't do anything
    Given i set to [request][content][name] value "Test anon"
    Given i set to [route][params][project] value 1
    Given i set to [route][params][task] value 1
    When i send a patch on route api_projects_tasks_update
    Then the status code should be 401
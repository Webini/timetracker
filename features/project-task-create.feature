Feature:
  In order to create a task
  As an admin / super admin i can create tasks for all projects
  As a project manager / user i can create tasks for projects where i've the create permission
  As an anonymous i can't do anything

  Background:
    Given a project saved in [project]
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline: As an admin / super admin i can create tasks for all projects
    Given i am an user of type <userType>
    Given i set to [request][content] values:
    """
    {
        "name": "Test project",
        "description": "simple description"
    }
    """
    When i send a put on route api_projects_tasks_create
    Then the status code should be 200
    Then the response item [id] should not be empty
    Examples:
       | userType    |
       | admin       |
       | super admin |

  Scenario Outline: As a project manager / user i can create tasks for projects where i've the create permission
    Given i am an user of type <userType>
    Given an user [me] assigned to project [project] with permission <permission>
    Given i set to [request][content] values:
    """
    {
        "name": "test task",
        "description": "simple description"
    }
    """
    When i send a put on route api_projects_tasks_create
    Then the status code should be <statusCode>
    Examples:
      | userType        | permission  | statusCode |
      | project manager | create task | 200        |
      | user            | create task | 200        |
      | project manager | none        | 403        |
      | user            | none        | 403        |


  Scenario: As an anonymous i can't do anything
    Given i set to [route][params][project] value 1
    When i send a put on route api_projects_tasks_create
    Then the status code should be 401

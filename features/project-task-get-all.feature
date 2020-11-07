Feature:
  In order to retrieve all tasks from a project
  As admin / super admin i can read all tasks in all projects
  As a project manager / user i can read all tasks from projects where i'm assigned
  As anon i can't read anything
  As any allowed user i should be able to search in tasks

  Background:
    Given a project saved in [project]
    Given an user of type project manager saved in [fake]
    Given a new task created for project [project]
    Given a new task created for project [project] saved in [task]
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline:
    As admin / super admin i can read all tasks in all projects
    As a project manager / user i can read all tasks from projects where i'm assigned
    Given i am an user of type <role>
    Given an user <assigned> assigned to project [project] with permission <assignedPerm>
    When i send a get on route api_projects_tasks_search
    Then the status code should be <expectedStatus>
    Examples:
         | role            | assigned | assignedPerm | expectedStatus |
         | super admin     | [fake]   | none         | 200            |
         | super admin     | [me]     | cud          | 200            |
         | admin           | [fake]   | none         | 200            |
         | admin           | [me]     | cud          | 200            |
         | project manager | [fake]   | none         | 403            |
         | project manager | [me]     | none         | 200            |
         | user            | [fake]   | none         | 403            |
         | user            | [me]     | cud          | 200            |



  Scenario: As any allowed user i should be able to search in tasks
    Given i am an user of type super admin
    Given i set to [request][parameters][search] the value of [task].name
    When i send a get on route api_projects_tasks_search
    Then the status code should be 200
    And the response item [pagination][totalCount] should be equal to 1

  Scenario: As any allowed user i should be able to search in tasks
    Given i am an user of type super admin
    When i send a get on route api_projects_tasks_search
    Then the status code should be 200
    And the response item [pagination][totalCount] should be equal to 2

  Scenario: As anon i can't read anything
    When i send a get on route api_projects_tasks_search
    Then the status code should be 401

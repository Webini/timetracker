Feature:
  As a super administrator, administrator or project manager
  i want to create a new project in order to add tasks
  As an anonymous i can't create a project

  Scenario Outline:
    As a super administrator, administrator or project manager
    i want to create a new project in order to add tasks
    Given i am an user of type <role>
    Given i set to [request][content] value {"name": "<role> project"}
    Then i send a put on route api_projects_create
    And the status code should be <expectedStatus>
    Examples:
     | role            | expectedStatus |
     | super admin     | 200            |
     | admin           | 200            |
     | project manager | 200            |
     | user            | 403            |

  Scenario: As an anonymous i can't create a project
    Given i set to [request][content] value {"name": "Anonymous project"}
    When i send a put on route api_projects_create
    And the status code should be 401
Feature:
  In order to search / get projects
  As any connected users i can search projects by name
  As an administrator / super administrator i can see all projects
  As a project manager / user i can only see projects where i'm assigned
  As an anonymous i can't search projects

  Background:
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test project" created by [users][projectCreator] saved in [trash]

  Scenario Outline: As an admin / super administrator i can see all projects
    Given i am an user of type <role>
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data][0][name] should be equal to "Test project"
    Examples:
      | role        |
      | super admin |
      | admin       |

  Scenario Outline: As a project manager / user i can only see projects where i'm assigned
    Given i am an user of type <role>
    Given i create a project named "Should see me" saved in [trash]
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data] should count 1
    And the response item [data][0][name] should be equal to "Should see me"
    Examples:
      | role            |
      | project manager |
      | user            |

  Scenario: As any connected users i can search projects by name
    Given i am an user of type super admin
    Given i create a project named "Should not see me" saved in [trash]
    Given i set to [request][parameters][search] value "TEST"
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data] should count 1
    And the response item [data][0][name] should be equal to "Test project"

  Scenario: As an anonymous i can't search projects
    When i send a get on route api_projects_search
    Then the status code should be 401

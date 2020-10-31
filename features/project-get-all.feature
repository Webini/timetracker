Feature:
  In order to search / get projects
  As any connected users i can search projects by name
  As an administrator / super administrator i can see all projects
  As a project manager / user i can only see projects where i'm assigned
  As an anonymous i can't search projects

  Scenario: As an super administrator i can see all projects
    Given i am an user of type super admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test SA" created by [users][projectCreator] saved in [trash]
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data][0][name] should be equal to "Test SA"

  Scenario: As an administrator i can see all projects
    Given i am an user of type admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test admin" created by [users][projectCreator] saved in [trash]
    Given i create a project named "Test myself" saved in [trash]
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data] should count 2

  Scenario: As a project manager i can only see projects where i'm assigned
    Given i am an user of type project manager
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Should not see" created by [users][projectCreator] saved in [trash]
    Given i create a project named "Test pm" saved in [trash]
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data] should count 1
    And the response item [data][0][name] should be equal to "Test pm"

  Scenario: As an user i can only see projects where i'm assigned
    Given i am an user of type user
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Should not see me" created by [users][projectCreator] saved in [projects][hidden]
    Given a project named "Should see me" created by [users][projectCreator] saved in [projects][see]
    Given i assign user [me] to project [projects][see] with permission crud
    When i send a get on route api_projects_search
    Then the status code should be 200
    And the response item [data] should count 1
    And the response item [data][0][name] should be equal to "Should see me"

  Scenario: As an anonymous i can't search projects
    When i send a get on route api_projects_search
    Then the status code should be 401

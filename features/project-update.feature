Feature:
  In order to modify an existing project,
  As a Super administrator or administrator i want to edit any projects
  As a Project Manager i want to edit only projects where i'm assigned as project admin
  As an user i can't edit any projects
  As an anonymous i can't edit anything

  Scenario: As a Super administrator i want to edit any projects
    Given i am an user of type super admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test project sa" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project sa"
    Then i send a patch on route api_projects_update
    And the status code should be 200
    And the response item [id] should not be empty
    And the response item [name] should be equal to "New project sa"

  Scenario: As an administrator i want to edit any projects
    Given i am an user of type admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test project admin" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project admin"
    Then i send a patch on route api_projects_update
    And the status code should be 200
    And the response item [id] should not be empty
    And the response item [name] should be equal to "New project admin"

  Scenario: As a Project Manager i want to edit only projects where i'm assigned as project admin
    Given i am an user of type project manager
    Given i create a project named "Test project pm" saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project pm"
    Then i send a patch on route api_projects_update
    And the status code should be 200
    And the response item [id] should not be empty
    And the response item [name] should be equal to "New project pm"

  Scenario: Project manager try to edit project of another user
    Given i am an user of type project manager
    Given an user of type admin saved in [users][projectCreator]
    Given a project named "Test not my project" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project pm"
    Then i send a patch on route api_projects_update
    And the status code should be 403

  Scenario: User try to edit a project
    Given i am an user of type user
    Given an user of type admin saved in [users][projectCreator]
    Given a project named "Test not my project" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project pm"
    Then i send a patch on route api_projects_update
    And the status code should be 403

  Scenario: As an anonymous i can't edit anything
    Given an user of type admin saved in [users][projectCreator]
    Given a project named "Test not my project" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    And i set to [request][content][name] value "New project pm"
    Then i send a patch on route api_projects_update
    And the status code should be 401

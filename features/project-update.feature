Feature:
  In order to modify an existing project,
  As a Super admin / admin i want to edit any projects
  As a Project Manager i want to edit only projects where i'm assigned as project admin
  As a Project Manager i can't edit projects where i'm not assigned as project admin
  As an user i can't edit any projects
  As an anonymous i can't edit anything

  Background:
    Given a project saved in [project]
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline: As a Super admin / admin i want to edit any projects
    Given i am an user of type <role>
    Given i set to [request][content][name] value "New project"
    When i send a patch on route api_projects_update
    Then the status code should be 200
    And the response item [id] should not be empty
    And the response item [name] should be equal to "New project"
    Examples:
      | role        |
      | super admin |
      | admin       |

  Scenario: As a Project Manager i want to edit only projects where i'm assigned as project admin
    Given i am an user of type project manager
    Given an user [me] assigned to project [project] with permission admin
    Given i set to [request][content][name] value "New project pm"
    When i send a patch on route api_projects_update
    Then the status code should be 200
    And the response item [id] should not be empty
    And the response item [name] should be equal to "New project pm"

  Scenario: As a Project Manager i can't edit projects where i'm not assigned as project admin
    Given i am an user of type project manager
    Given i set to [request][content][name] value "New project pm"
    When i send a patch on route api_projects_update
    Then the status code should be 403

  Scenario: User try to edit a project
    Given i am an user of type user
    Given i set to [request][content][name] value "New project pm"
    When i send a patch on route api_projects_update
    Then the status code should be 403

  Scenario: As an anonymous i can't edit anything
    Given i set to [request][content][name] value "New project pm"
    When i send a patch on route api_projects_update
    Then the status code should be 401

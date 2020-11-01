Feature:
  In order to see a project data
  As an Administrator / Super Admin i want to see full information about all projects
  As a Project Manager / User i want to see short information of projects where i'm assigned but not admin
  As a Project Manager / User i must not see information about projects where i'm not assigned
  As a Project Manager with project admin permission i want to see full information about it
  As an anonymous i shouldn't see anything

  Background:
    Given a project saved in [project]
    Given i set to [route][params][project] the value of [project].id

  Scenario Outline: As an Administrator / Super Admin i want to see full information about all projects
    Given i am an user of type <role>
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    And the response should have keys [id],[name],[guid],[providerConfiguration]
    Examples:
      | role        |
      | super admin |
      | admin       |

  Scenario: As a Project Manager with project admin permission i want to see full information about it
    Given i am an user of type project manager
    Given an user [me] assigned to project [project] with permission admin
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    And the response should have keys [id],[name],[guid],[providerConfiguration]

  Scenario: As a project manager without project admin permission i must not see full information
    Given i am an user of type project manager
    Given an user [me] assigned to project [project] with permission cud
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    Then the response should not have keys [guid],[providerConfiguration]
    Then the response should have keys [id],[name]

  Scenario: As an user without project admin permission i must not see full information
    Given i am an user of type user
    Given an user [me] assigned to project [project] with permission cud
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    Then the response should not have keys [guid],[providerConfiguration]
    Then the response should have keys [id],[name]

  Scenario Outline: As a Project Manager / user i must not see information about projects where i'm not assigned
    Given i am an user of type <role>
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_get_one
    Then the status code should be 403
    Examples:
      | role            |
      | project manager |
      | user            |

  Scenario: As an anonymous i shouldn't see anything
    When i send a get on route api_projects_get_one
    Then the status code should be 401
Feature:
  In order to see a project data
  As an Administrator / Super Admin i want to see full information about all projects
  As a Project Manager / User i want to see short information of projects where i'm assigned but not admin
  As a Project Manager / User i must not see information about projects where i'm not assigned
  As a Project Manager with project admin permission i want to see full information about it
  As an anonymous i shouldn't see anything

  Scenario: As a Super Admin i want to see full information about all projects
    Given i am an user of type super admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test SA" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 200
    And the response should have keys [id],[name],[guid],[providerConfiguration]

  Scenario: As a Super Admin i want to see full information about all projects
    Given i am an user of type admin
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test Admin" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 200
    And the response should have keys [id],[name],[guid],[providerConfiguration]

  Scenario: As a Project Manager with project admin permission i want to see full information about it
    Given i am an user of type project manager
    Given i create a project named "Test project" saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 200
    And the response should have keys [id],[name],[guid],[providerConfiguration]

  Scenario: As a project manager without project admin permission i must not see full information
    Given i am an user of type project manager
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test limited infos" created by [users][projectCreator] saved in [project]
    Given i assign user [me] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    Then the response should not have keys [guid],[providerConfiguration]
    Then the response should have keys [id],[name]

  Scenario: As an user without project admin permission i must not see full information
    Given i am an user of type user
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test limited infos" created by [users][projectCreator] saved in [project]
    Given i assign user [me] to project [project] with permission crud
    Given i set to [route][params][project] the value of [project].id
    When i send a get on route api_projects_get_one
    Then the status code should be 200
    Then the response should not have keys [guid],[providerConfiguration]
    Then the response should have keys [id],[name]

  Scenario: As a Project Manager i must not see information about projects where i'm not assigned
    Given i am an user of type project manager
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test not assigned" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 403

  Scenario: As an user i must not see information about projects where i'm not assigned
    Given i am an user of type user
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test not assigned" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 403

  Scenario: As an anonymous i shouldn't see anything
    Given an user of type project manager saved in [users][projectCreator]
    Given a project named "Test not assigned" created by [users][projectCreator] saved in [project]
    And i set to [route][params][project] the value of [project].id
    Then i send a get on route api_projects_get_one
    And the status code should be 401
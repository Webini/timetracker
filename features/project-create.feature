@current
Feature:
  As a super administrator, administrator or project manager
  i want to create a new project in order to add tasks

  Scenario:
    Given i am an user of type super admin
    And i set to [request][content] value {"name": "SA Project"}
    Then i send a put on route api_projects_create
    And the status code should be 200
    And the response item [name] should be equal to "SA Project"

  Scenario:
    Given i am an user of type admin
    And i set to [request][content] value {"name": "Admin Project"}
    Then i send a put on route api_projects_create
    And the status code should be 200
    And the response item [name] should be equal to "Admin Project"

  Scenario:
    Given i am an user of type project manager
    And i set to [request][content] value {"name": "PM Project"}
    Then i send a put on route api_projects_create
    And the status code should be 200
    And the response item [name] should be equal to "PM Project"

  Scenario:
    Given i am an user of type user
    And i set to [request][content] value {"name": "User Project"}
    Then i send a put on route api_projects_create
    And the status code should be 403
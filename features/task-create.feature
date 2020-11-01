Feature:
  In order to create a task
  As an admin / super admin i can create tasks for all projects
  As a project manager / user i can create tasks for projects where i've the create permission
  As an anonymous i can't do anything

  Scenario: As a super admin i can create tasks for all projects
    Given i am an user of type super admin
    Given a project saved in [project]
    Given i set to [request][content] values:
    """
    {
        "name": "test sa",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 200
    Then the response item [id] should not be empty

  Scenario: As an admin i can create tasks for all projects
    Given i am an user of type admin
    Given a project saved in [project]
    Given i set to [request][content] values:
    """
    {
        "name": "test admin",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 200
    Then the response item [id] should not be empty

  Scenario: As an user i can create tasks for projects where i've the create permission
    Given i am an user of type user
    Given a project saved in [project]
    Given i assign user [me] to project [project] with permission create task
    Given i set to [request][content] values:
    """
    {
        "name": "test user",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 200
    Then the response item [id] should not be empty

  Scenario: As a project manager i can create tasks for projects where i've the create permission
    Given i am an user of type project manager
    Given a project saved in [project]
    Given i assign user [me] to project [project] with permission create task
    Given i set to [request][content] values:
    """
    {
        "name": "test pm",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 200
    Then the response item [id] should not be empty

  Scenario: As a project manager i can't create tasks for projects where i haven't the create permission
    Given i am an user of type project manager
    Given a project saved in [project]
    Given i set to [request][content] values:
    """
    {
        "name": "test pm",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 403

  Scenario: As an user i can't create tasks for projects where i haven't the create permission
    Given i am an user of type user
    Given a project saved in [project]
    Given i set to [request][content] values:
    """
    {
        "name": "test user",
        "description": "simple description"
    }
    """
    Given i set to [route][params][project] the value of [project].id
    When i send a put on route api_projects_tasks_create
    Then the status code should be 403

  Scenario: As an anonymous i can't do anything
    Given i set to [route][params][project] value 1
    When i send a put on route api_projects_tasks_create
    Then the status code should be 401

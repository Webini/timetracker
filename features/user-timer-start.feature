Feature: In order to create a timer for an user
  As an super admin / admin i can create a timer for all users.
  As a project manager / user i can create a timer for projects where i'm assigned.
  As a project manager i can create timer for users assigned to projects where i'm admin.
  As an anon i can't do anything.
  //voir a bouger la feature ci dessous dans le segment /timers
  As an allowed user, i can add a stopped timer with a start date and a duration.
  As an allowed user, i can't start a timer if i have another timer not stopped.
  As an allowed user, i can automatically stop my running timer and start a new one if
  the endpoint is called with a force parameter.

  Background:
    Given a project saved in [project]
    Given an user of type user saved in [fakeUser]
    Given a new task created for project [project] saved in [task]
    Given an user [fakeUser] assigned to project [project] with permission none
    Given i set to [request][content][task] the value of [task].id
    Given i set to [route][params][user] the value of [fakeUser].id

  Scenario Outline: As an super admin / admin i can create a timer for all users.
    Given i am an user of type <role>
    When i send a put on route api_users_timer_start
    Then the status code should be <expectedStatus>
    Examples:
     | role            | expectedStatus |
     | super admin     | 200            |
     | admin           | 200            |
     | project manager | 403            |
     | user            | 403            |

  Scenario Outline: As a project manager / user i can create a timer for projects where i'm assigned.
    Given i am an user of type <role>
    Given an user [me] assigned to project [project] with permission none
    Given i set to [route][params][user] the value of [me].id
    When i send a put on route api_users_timer_start
    Then the status code should be <expectedStatus>
    Examples:
       | role            | expectedStatus |
       | project manager | 200            |
       | user            | 200            |

  Scenario Outline: As a project manager i can create timer for users assigned to projects where i'm admin.
    Given i am an user of type <role>
    Given an user [me] assigned to project [project] with permission <permission>
    When i send a put on route api_users_timer_start
    Then the status code should be <expectedStatus>
    Examples:
        | role            | permission | expectedStatus |
        | project manager | none       | 200            |
        | user            | cud        | 403            |
        | user            | none       | 403            |

  Scenario: As an anon i can't do anything.
    When i send a put on route api_users_timer_start
    Then the status code should be 401

  Scenario: As an allowed user, i can add a stopped timer with a start date and a duration.
    Given i am an user of type admin
    Given i set to [request][content] values:
    """
    {
        "startedAt": "2020-11-01T23:00:00.000Z",
        "hours": 1,
        "minutes": 10
    }
    """
    Given i set to [request][content][task] the value of [task].id
    When i send a put on route api_users_timer_start
    Then the status code should be 200
    And the response item [id] should not be empty
    And the response item [stoppedAt] should not be empty
    And the response item [startedAt] should not be empty

  Scenario: As an allowed user, i can't start a timer if i have another timer not stopped.
    Given i am an user of type admin
    Given i have a running timer for task [task]
    Given i set to [route][params][user] the value of [me].id
    When i send a put on route api_users_timer_start
    Then the status code should be 409

  Scenario:
    As an allowed user, i can automatically stop my running timer and start a new one if
    the endpoint is called with a force parameter.
    Given i am an user of type admin
    Given i have a running timer for task [task]
    Given i set to [request][content][force] value true
    Given i set to [route][params][user] the value of [me].id
    When i send a put on route api_users_timer_start
    Then the status code should be 200

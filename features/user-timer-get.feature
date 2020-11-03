Feature:
  As an admin / super admin i should be able to retrieve running timer from all users
  As an user / project manager admin i should get only my running timer
  As anon i can't do anything
  As any allowed user i should get data with task name

  Background:
    Given a project saved in [project]
    Given a new task created for project [project] saved in [task]

  Scenario Outline:
    As an admin / super admin i should be able to retrieve running timer from all users
    As an user / project manager admin i should get only my running timer
    Given i am an user of type <role>
    Given an user of type user saved in [fakeUser]
    Given an user <timerUser> assigned to project [project] with permission none
    Given a running timer for task [task] and user <timerUser>
    Given i set to [route][params][user] the value of <timerUser>.id
    When i send a get on route api_users_timer_get
    Then the status code should be <expectedStatus>
    Examples:
       | role            | timerUser  | expectedStatus |
       | super admin     | [fakeUser] | 200            |
       | super admin     | [me]       | 200            |
       | admin           | [fakeUser] | 200            |
       | admin           | [me]       | 200            |
       | project manager | [fakeUser] | 403            |
       | project manager | [me]       | 200            |
       | user            | [fakeUser] | 403            |
       | user            | [me]       | 200            |

  Scenario: As anon i can't do anything
    Given i set to [route][params][user] value 1
    When i send a get on route api_users_timer_get
    Then the status code should be 401

  Scenario: As any allowed user i should get data with task name
    Given i am an user of type user
    Given an user [me] assigned to project [project] with permission none
    Given i have a running timer for task [task]
    Given i set to [route][params][user] the value of [me].id
    When i send a get on route api_users_timer_get
    Then the status code should be 200
    And the response item [task][name] should not be empty


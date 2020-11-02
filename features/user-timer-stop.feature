Feature:
  In order to stop a running timer
  As a super admin / admin i can stop running timers of any users
  As a project manager / user i can stop my running timer
  As an anon i can't do nothing

  Scenario Outline:
    Given i am an user of type <role>
    Given an user of type user saved in [fakeUser]
    Given a project saved in [project]
    Given a new task created for project [project] saved in [task]
    Given an user <timerUser> assigned to project [project] with permission none
    Given a running timer for task [task] and user <timerUser>
    Given i set to [route][params][user] the value of <stoppedUser>.id
    When i send a post on route api_users_timer_stop
    Then the status code should be <expectedStatus>
    Examples:
       | role            | timerUser  | stoppedUser | expectedStatus |
       | super admin     | [fakeUser] | [fakeUser]  | 204            |
       | admin           | [fakeUser] | [fakeUser]  | 204            |
       | project manager | [fakeUser] | [fakeUser]  | 403            |
       | user            | [fakeUser] | [fakeUser]  | 403            |
       | super admin     | [me]       | [me]        | 204            |
       | admin           | [me]       | [me]        | 204            |
       | project manager | [me]       | [me]        | 204            |
       | user            | [me]       | [me]        | 204            |
       | user            | [me]       | [fakeUser]  | 404            |

  Scenario: As an anon i can't do nothing
    Given i set to [route][params][user] value 1
    When i send a post on route api_users_timer_stop
    Then the status code should be 401
Feature: In order to create a timer for an user
  As an super admin / admin i can create a timer for all users.
  As a project manager / user i can create a timer for projects where i'm assigned.
  As a project manager i can create timer for users assigned to projects where i'm admin.
  As an anon i can't do anything.
  As an allowed user, i can add a stopped timer with a start date and a duration.
  As an allowed user, i can't start a timer if i have another timer not stopped.
  As an allowed user, i can automatically stop it and start a new one if the endpoint
  is called with a force parameter.

# sentence possibilities
#  Given a timer running for task [task] saved in [path]
#  Given a timer runned 3 days ago during 5 hours for task [task]
#  Given i have a timer running for task [task] saved in [path]
#  Given my runned timer 3 days ago during 5 hours for task [task]

  Background:
    Given a project saved in [project]
    Given an user of type user saved in [fakeUser]
    Given a new task created for project [project] saved in [task]
    Given an user [fakeUser] assigned to project [project] with permission none
    Given i set to [route][params][user] the value of [fakeUser].id

  @current 
  Scenario: As an super admin / admin i can create a timer for all users.
    Given i am an user of type super admin
    Given i set to [request][content][task] the value of [task].id
    When i send a put on route api_users_timers_start
    Then the status code should be 200
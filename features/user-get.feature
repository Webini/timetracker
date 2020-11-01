Feature:
  In order to retrieve single user data
  As myself, i want to retrieve full information about me
  As project managers, administrator and super administrator i want
  to retrieve full information of everyone
  As any logged users i want to retrieve minimal information of everyone
  As an anonymous i can't get user information

  Scenario Outline:
    As myself, i want to retrieve full information about me
    As project managers, administrator and super administrator i want
    to retrieve full information of everyone
    As any logged users i want to retrieve minimal information of everyone
    Given i am an user of type <role>
    Given an user of type super admin saved in [fake]
    Given i set to [route][params][user] the value of <user>.id
    When i send a get on route api_users_get_one
    Then the status code should be 200
    And the response should have keys <expectedKeys>
    And the response should not have keys <unexpectedKeys>
    Examples:
       | role            | user   | expectedKeys                           | unexpectedKeys                     |
       | super admin     | [fake] | [phoneNumber],[email],[emailValidated] | [password],[plainPassword]         |
       | admin           | [fake] | [phoneNumber],[email],[emailValidated] | [password],[plainPassword]         |
       | project manager | [fake] | [phoneNumber],[email],[emailValidated] | [password],[plainPassword]         |
       | user            | [fake] | [firstName],[lastName]                 | [email],[password],[plainPassword] |
       | user            | [me]   | [phoneNumber],[email],[emailValidated] | [password],[plainPassword]         |

  Scenario: As an anonymous i can't get user information
    Given an user of type user saved in [users][u]
    Given i set to [route][params][user] the value of [users][u].id
    When i send a get on route api_users_get_one
    Then the status code should be 401
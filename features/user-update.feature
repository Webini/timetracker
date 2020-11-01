Feature:
  In order to update information about users
  As a super administrator i want to edit everybody
  As an administrator i want to edit everybody except super admin and other admin
  As a project manager i want to edit only users
  As myself, i want to update myself
  As an anonymous i can't edit anybody

  Scenario Outline:
    As a super administrator i want to edit everybody
    As an administrator i want to edit everybody except super admin and other admin
    As a project manager i want to edit only users
    Given i am an user of type <role>
    Given an user of type <otherRole> saved in [users][other]
    Given i set to [route][params][user] the value of [users][other].id
    Given i set to [request][content][firstName] value "New First Name"
    When i send a patch on route api_users_update
    Then the status code should be <expectedStatus>
    Examples:
       | role            | otherRole       | expectedStatus |
       | super admin     | super admin     | 200            |
       | super admin     | admin           | 200            |
       | super admin     | project manager | 200            |
       | super admin     | user            | 200            |
       | admin           | super admin     | 403            |
       | admin           | admin           | 403            |
       | admin           | project manager | 200            |
       | admin           | user            | 200            |
       | project manager | super admin     | 403            |
       | project manager | admin           | 403            |
       | project manager | project manager | 403            |
       | project manager | user            | 200            |
       | user            | super admin     | 403            |
       | user            | admin           | 403            |
       | user            | project manager | 403            |
       | user            | user            | 403            |

  Scenario: As an user i can edit myself
    Given i am an user of type user
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [me].id
    And i send a patch on route api_users_update
    Then the status code should be 200

  Scenario: As an user i can't edit another user
    Given i am an user of type user
    Given an user of type user saved in [users][user]
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [users][user].id
    And i send a patch on route api_users_update
    Then the status code should be 403

  Scenario: As an anonymous i can't edit anybody
    Given an user of type user saved in [users][user]
    And i set to [route][params][user] the value of [users][user].id
    And i send a patch on route api_users_update
    Then the status code should be 401

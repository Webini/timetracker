Feature:
  In order to update informations about users
  As a super administrator i want to edit everybody
  As an administrator i want to edit everybody except super administrators
  As myself, i want to update myself

  Scenario: As a Super Administrator i can update another Super Administrator
    Given i am an user of type super admin
    Given an user of type super admin saved in [users][superAdmin]
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [users][superAdmin].id
    And i send a patch on route api_users_update
    Then the status code should be 200

  Scenario: As an Administrator i can't update another Administrator
    Given i am an user of type admin
    Given an user of type admin saved in [users][admin]
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [users][admin].id
    And i send a patch on route api_users_update
    Then the status code should be 403

  Scenario: As an Administrator i can update a project manager
    Given i am an user of type admin
    Given an user of type project manager saved in [users][pm]
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [users][pm].id
    And i send a patch on route api_users_update
    Then the status code should be 200

  Scenario: As a project manager i can't update an user
    Given i am an user of type project manager
    Given an user of type user saved in [users][user]
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [users][user].id
    And i send a patch on route api_users_update
    Then the status code should be 403

  Scenario: As an user i can edit myself
    Given i am an user of type user
    When i set to [request][content][firstName] value "New First Name"
    And i set to [route][params][user] the value of [user].id
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

Feature: As any users i expect to retrieve supported timezone
  Scenario:
    When i send a get on route api_timezones_get_all
    Then the status code should be 200
    And the response item [0] should not be empty
Feature: Testing sample request

  Scenario: Add new artist
    Given the request body is:
        """
        {
            "name": "Acceptance Test Artist 1"
        }
        """
    When I request "/artists" using HTTP "POST"
    Then the response code is 201
    And the response body contains JSON:
        """
        {
            "name": "Acceptance Test Artist 1"
        }
        """

  Scenario: Duplicated artist should return status code 409
    Given the request body is:
        """
        {
            "name": "Acceptance Test Artist 1"
        }
        """
    When I request "/artists" using HTTP "POST"
    Then the response code is 409

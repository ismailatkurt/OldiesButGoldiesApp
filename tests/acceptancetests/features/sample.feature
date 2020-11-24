Feature: Testing sample request

  Scenario: Add new artist
    Given the request body is:
        """
        {
            "name": "Acceptance Test Artist 1"
        }
        """
    When I request "/artists" using HTTP "POST"
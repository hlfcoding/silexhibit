Feature: Site

  Scenario: Viewing homepage exhibit
    When I go to homepage
    Then the url should match "/"
    And the model should return complete exhibit data for "/"
    And the view should properly transform exhibit data for "/"
    And the response status code should be 200
    And the response should contain a full page


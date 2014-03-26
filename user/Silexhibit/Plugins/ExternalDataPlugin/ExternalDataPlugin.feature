Feature: Plugin

  # This needs to be updated per the plugin config.

  Scenario: Github feed
    When I go to "/st-external-data/github/"
    And I reload the page
    Then the response status code should be 200
    And the response should be in "XML"
    And the response header "X-Cache" should be "HIT from GuzzleCache"

  Scenario: Userscripts feed
    When I go to "/st-external-data/userscripts/"
    And I reload the page
    Then the response status code should be 200
    And the response should be in "XML"
    And the response header "X-Cache" should be "HIT from GuzzleCache"

  Scenario: Tumblr feed
    When I go to "/st-external-data/tumblr/"
    And I reload the page
    Then the response status code should be 200
    And the response should be in "JSON"
    And the response header "X-Cache" should be "HIT from GuzzleCache"

  Scenario: Twitter feed
    When I go to "/st-external-data/twitter/"
    And I reload the page
    Then the response status code should be 200
    And the response should be in "JSON"
    And the response header "X-Cache" should be "HIT from GuzzleCache"

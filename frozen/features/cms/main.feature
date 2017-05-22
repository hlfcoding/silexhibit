Feature: CMS

  Scenario: Viewing homepage
    When I go to "/st-studio/"
    Then the response status code should be 200

  Scenario: Fetching exhibits from API
    When I go to "/st-exhibit/"
    Then the response status code should be 200
    And the response should be in "JSON"
    And the JSON response should be a list
    And the first exhibit JSON should be complete

  Scenario: Fetching exhibit from API
    When I go to "/st-exhibit/1"
    Then the response status code should be 200
    And the response should be in "JSON"
    And the exhibit detail JSON should be complete

  @javascript
  Scenario: View exhibits
    Given I am on "/st-studio/"
    And the DOM is ready
    Then I should see a ".app-content > section.exhibit-collection" element

  @javascript
  Scenario: Edit exhibits
    Given I am on "/st-studio/"
    And the DOM is ready
    When I click the first ".section-exhibits .exhibit-cell .exhibit-edit" element
    And I wait for a ".app-content > section.exhibit-form" element
    Then I should see a ".app-content > section.exhibit-form" element
    And the URL should match "/st-studio/exhibit/\d+"
      When I click the first ".exhibit-form .exhibit-save" element
      And I wait for the network
      Then I should not see any visible ".exhibit-form .error" elements


@local @local_profile_directory
Feature: Basic tests for Profile directory

  @javascript
  Scenario: Plugin local_profile_directory appears in the list of installed additional plugins
    Given I log in as "admin"
    When I navigate to "Plugins > Plugins overview" in site administration
    And I follow "Additional plugins"
    Then I should see "Profile directory"
    And I should see "local_profile_directory"

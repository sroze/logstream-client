Feature:
  In order to give real time feedback to customers
  As a developer
  I want to be able to create and update logs

  Scenario: Container log
    When I create an empty container log
    Then the log should be successfully created

  Scenario: Log children
    Given I have an empty container log
    When I create a text log containing "Foo" under the container log
    Then the log should be successfully created

  Scenario: Update log's status
    Given I have an empty container log
    And I have a text log
    When I update the status of the log with "success"
    Then the log should be successfully updated


Feature: Test di prova

    Scenario: Prova del test
        Given I am on "/"
        When pint last response
        And I press "invia"
        Then print current URL
        And show last response
        And the "response" element should contain "Ricevuto [test]"

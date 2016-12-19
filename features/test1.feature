
Feature: Test di prova

    Scenario: Prova del test
        Given I am on "/"
        When print last response
        And I press "Invia"
        Then print current URL
        And show last response
        And the "response" element should contain "Ricevuto [test]"

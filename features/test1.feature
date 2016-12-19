
Feature: Test di prova

    Scenario: Prova del test
        Given I am on "/"
        When print last response
        And I fill in "message" with "test"
        And I press "Invia"
        Then I should see "Ricevuto [test]" in the "#response" element

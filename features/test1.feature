
Feature: Test di prova

    Scenario: Invio messaggio di test non crittato
        Given I am on "/"
        When I fill in "message" with "test"
        And I press "Invia"
        And I wait ajax
        Then I should see "Ricevuto [test]" in the "#response" element

    Scenario: Invio messaggio di test crittato
        Given I am on "/"
        When I fill in "message" with "test"
        And I check "cryptionFlag"
        And I press "Invia"
        And I wait ajax
        Then I should see "Ricevuto [test]" in the "#response" element

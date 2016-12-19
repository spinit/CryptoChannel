
Feature: Test di prova

    Scenario: Prova del test
        Given imposto "message" con "test"
        When clicco su "invia"
        Then casella "response" ha valore "Ricevuto [test]"

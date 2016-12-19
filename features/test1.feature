
Feature: Test di prova

    Scenario: Prova del test
        Given imposto "dati" con "test"
        When clicco su "invia"
        Then casella "result" ha valore "Recuperato [ test]"

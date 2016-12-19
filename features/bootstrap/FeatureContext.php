<?php
 
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
{
    /**
     * @Given /^imposto "([^"]*)" con "([^"]*)"$/
     */
    public function impostoCon($arg1, $arg2)
    {
        $element = $this->getSession()->getPage()->findById($arg1);
        var_dump($arg1, $arg2, $element);
        echo $this->getSession()->getPage()->getHtml();
        $element->setValue($arg2);
    }

    /**
     * @When /^clicco su "([^"]*)"$/
     */
    public function cliccoSu($arg1)
    {
        $element = $this->getSession()->getPage()->findButton($arg1);
        $element->click();
        $this->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );  
        
    }

    /**
     * @Then /^casella "([^"]*)" ha valore "([^"]*)"$/
     */
    public function casellaHaValore($arg1, $arg2)
    {
        $element = $this->getSession()->getPage()->findById($arg1);
        if ($element->getText()!=$arg2) {
            throw new \Exception();
        }
    }

}
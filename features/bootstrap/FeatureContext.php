<?php
 
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
{
    /**
     * @Given /^imposto "([^"]*)" con "([^"]*)"$/
     */
    public function impostoCon($arg1, $arg2)
    {
        print_r(get_class_methods($this->getSession()->getDriver()));
    }

    /**
     * @When /^clicco su "([^"]*)"$/
     */
    public function cliccoSu($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^casella "([^"]*)" ha valore "([^"]*)"$/
     */
    public function casellaHaValore($arg1, $arg2)
    {
        throw new PendingException();
    }

}
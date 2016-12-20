<?php
use Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\SuiteEvent;
 
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
{
    /**
     * @Given /^I wait ajax$/
     */
    public function iWaitAjax()
    {
        $this->getSession()->wait(10000, '(0 === Krypto.isAjaxActive())');
    }
}
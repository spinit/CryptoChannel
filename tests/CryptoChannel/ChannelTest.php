<?php
namespace CryptoChannel;

class ChannelTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->object = new Channel();
    }
    
    public function testInitJavascript()
    {
        $this->assertTrue(strlen($this->object->initJavascript('test','dir'))>0);
    }
}

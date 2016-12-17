<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;
ob_start();

class ChanneClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ChannelClient
     */
    private $object;
    
    public function setUp()
    {
        $dummy = new Base();
        $dummy->util(new TestUtil());
        $this->object = new ChannelClient();
        
        $this->urlbase = 'http://'.WEB_SERVER_HOST.':'.WEB_SERVER_PORT;
    }
    
    public function testTransferData()
    {
        $data = 'ok';
        $actual = $this->object->getContent($this->urlbase.'/index.php?echo=0', $data);
        $expected = sprintf('Ricevuto [%s]%s', $data, WEB_SERVER_PORT);
        $this->assertEquals($actual, $expected);
    }
    
    public function testTransferDataCripted()
    {
        $this->object->enableCryption();
        $data = 'ok';
        $actual = $this->object->getContent($this->urlbase.'/index.php?echo=0', $data);
        $expected = sprintf('Ricevuto [%s]%s', $data, WEB_SERVER_PORT);
        $this->assertEquals($actual, $expected);
    }
}

class TestUtil extends Util
{
    public function header($h)
    {
        return '';
    }
    public function session_start()
    {
        return '';
    }
}
<?php
namespace CryptoChannel;
ob_start();

class ChanneOptionlTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ChannelOption
     */
    private $object;
    
    public function setUp()
    {
    }
    
    public function testMethod()
    {
        $this->object = new ChannelOption();
        $this->assertEquals($this->object->getMethod(), 'POST');
        $this->object = new ChannelOption(array('method'=>'GET'));
        $this->assertEquals($this->object->getMethod(), 'GET');
    }
    public function testCrypting()
    {
        $this->object = new ChannelOption();
        $this->assertTrue($this->object->isCrypting());
        $this->object = new ChannelOption(array('crypting'=>false));
        $this->assertFalse($this->object->isCrypting());
    }
    public function testHeader()
    {
        $this->object = new ChannelOption('','');
        $this->assertContains('Cryption-Type: CryptoChannel', $this->object->getHeader());
        $this->object = new ChannelOption(array('crypting'=>false), array('un'=>'test'));
        $this->assertContains('Cookie: un=test;', $this->object->getHeader());
        $this->object = new ChannelOption(array('type'=>'un/test'));
        $this->assertContains('Content-Type: un/test', $this->object->getHeader());
        $this->object = new ChannelOption(array('type'=>'html'));
        $this->assertContains('Content-Type: text/html;', $this->object->getHeader());
    }
}

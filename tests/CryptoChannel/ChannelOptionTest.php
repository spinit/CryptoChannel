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
    public function testType()
    {
        $this->object = new ChannelOption();
        $this->assertEquals($this->object->getType(),'json');
        $this->object = new ChannelOption(array('type'=>'html'));
        $this->assertEquals($this->object->getType(),'html');
    }
    public function testHeader()
    {
        $this->object = new ChannelOption('','', $this);
        $this->assertContains('Cryption-Type: CryptoChannel', $this->object->getHeader());
        $this->object = new ChannelOption(array('crypting'=>false), array('un'=>'test'));
        $this->assertContains('Cookie: un=test;', $this->object->getHeader());
        $this->object = new ChannelOption(array('type'=>'un/test'));
        $this->assertContains('Content-Type: un/test', $this->object->getHeader());
        $this->object = new ChannelOption(array('type'=>'html'));
        $this->assertContains('Content-Type: text/html;', $this->object->getHeader());
        $this->object = new ChannelOption(array('headers'=>'Uno due tre'));
        $this->assertContains('Uno due tre', $this->object->getHeader());
        $this->object = new ChannelOption(array('headers'=>array('Uno due tre', 'quattro cinque e sei')));
        $this->assertContains('quattro cinque e sei', $this->object->getHeader());
    }
    
    public function getToken() {
        return 'test';
    }
}

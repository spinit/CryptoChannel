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
        $this->assertTrue($this->object->isCrypting());
        $this->object = new ChannelOption(array('crypting'=>false));
        $this->assertFalse($this->object->isCrypting());
    }
}

<?php
namespace CryptoChannel;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Base;
     */
    private $object;
    
    public function setUp()
    {
        $this->object = new Base();
    }
    
    public function testSingleton()
    {
        $expected = null;
        $actual = $this->object->singleton('test');
        $this->assertEquals($expected, $actual);
        $expected = 'ok';
        $actual = $this->object->singleton('test', $expected);
        $this->assertEquals($expected, $actual);
    }
    
    public function testUtil()
    {
        $actual = $this->object->util();
        $this->assertTrue($actual instanceof Util);
        $expected = 'ok';
        $actual = $this->object->util($expected);
        $this->assertEquals($expected, $actual);
    }
}

<?php
namespace CryptoChannel;

class RestoreSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var CryptoChannel/RestoreSession
     */
    private $object;
    
    public function setUp()
    {
        $this->object = new RestoreSession('prova','POST');
    }
    
    public function testStoring()
    {
        $expected = array('un'=>'test');
        $this->object->storeObject($expected);
        $actual = $this->object->loadObject();
        $this->assertEquals($actual, $expected);
    }
}

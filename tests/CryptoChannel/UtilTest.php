<?php
namespace CryptoChannel;
ob_start();

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ChannelOption
     */
    private $object;
    
    public function setUp()
    {
    }
    
    public function testLog()
    {
        if (is_file('/tmp/test-cryptochannel-util.log')) {
            unlink('/tmp/test-cryptochannel-util.log');
        }
        Util::setLogFile('');
        Util::log('test');
        $this->assertFalse(is_file('/tmp/test-cryptochannel-util.log'));
        
        
        Util::setLogFile('/tmp/test-cryptochannel-util.log');
        Util::log('test');
        $this->assertTrue(is_file('/tmp/test-cryptochannel-util.log'));
    }
}

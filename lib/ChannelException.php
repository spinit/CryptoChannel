<?php
namespace CryptoChannel;
/**
 * Gestione errori
 */
class ChannelException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function render() {
        return $this->getMessage();
    }
}

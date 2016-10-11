<?php
namespace CryptoChannel;

use CryptoChannel\RestoreInterface;

/**
 * Classe che permette la memorizzazione e il ripristino di un oggetto
 */
class RestoreSession implements RestoreInterface
{

    private $varname;
    private $varvar;
    
    public function __construct($varname, $varbuf = 'SESSION')
    {
        if (session_status() != PHP_SESSION_ACTIVE and $varbuf == 'SESSION') {
            session_start();
        }
        if (!is_array($varname)) {
            $varname = array($varname);
        }
        $varvar = '_'.$varbuf;
        $this->varname = $varname;
        
        global ${$varvar};
        $this->varvar = &${$varvar};
    }
    
    public function loadObject()
    {
        $session = $this->varvar;
        foreach($this->varname as $name) {
            $session = @$session[$name];
        }
        if (!$session) {
            return null;
        }
        return unserialize($session);
    }

    public function storeObject($data)
    {
        $session = &$this->varvar;
        foreach($this->varname as $name) {
            $value = &$session[$name];
            unset($session);
            $session = &$value;
        }
        $session = serialize($data);        
    }
}

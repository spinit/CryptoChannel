<?php
namespace CryptoChannel;

use CryptoChannel\RestoreInterface;

/**
 * Classe principale attraverso la quale crittare/decrittare i dati
 */
class RestoreSession implements RestoreInterface
{

    private $varname;

    public function __construct($varname)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!is_array($varname)) {
            $varname = array($varname);
        }
        $this->varname = $varname;
    }
    
    public function loadObject()
    {
        $session = $_SESSION;
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
        $session = &$_SESSION;
        foreach($this->varname as $name) {
            $value = &$session[$name];
            unset($session);
            $session = &$value;
        }
        $session = serialize($data);        
    }
}

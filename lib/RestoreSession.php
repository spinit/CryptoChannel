<?php
namespace CryptoChannel;

use CryptoChannel\RestoreInterface;

/**
 * Classe che permette la memorizzazione e il ripristino di un oggetto
 */
class RestoreSession extends Base implements RestoreInterface
{

    private $varname;
    private $varvar;
    private $closeSession = false;
    
    public function __construct($varname, $varbuf = 'SESSION')
    {
        if (!is_array($varname)) {
            $varname = array($varname);
        }
        $this->varname = $varname;
        $this->bufname = '_'.$varbuf;
    }
    private function open()
    {
        $this->closeSession = false;
        if ($this->util()->session_status() != PHP_SESSION_ACTIVE and $this->bufname == '_SESSION') {
            $this->util()->session_start();
            $this->closeSession = true;
        }
        global ${$this->bufname};
        $this->varvar = &${$this->bufname};
    }
    private function close()
    {
        if ($this->closeSession) {
            $this->util()->session_write_close();
        }
        unset($this->varvar);
    }
    public function loadObject()
    {
        $this->open();
        $session = $this->varvar;
        foreach($this->varname as $name) {
            $session = @$session[$name];
        }
        $this->close();
        if (!$session) {
            return null;
        }
        return unserialize($session);
    }

    public function storeObject($data)
    {
        $this->open();
        $session = &$this->varvar;
        foreach($this->varname as $name) {
            $value = &$session[$name];
            unset($session);
            $session = &$value;
        }
        $session = serialize($data);
        $this->close();
    }
}

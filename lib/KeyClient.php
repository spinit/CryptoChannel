<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;

/**
 * Description of KeyClient
 *
 * @author ermanno
 */
class KeyClient
{
    // chiave pubblica
    private $pubKey;
    // chiave simmetrica
    private $symKey;
    
    private $sourcer;
    
    // token locale generato in fase di generazione chiave simmetrica
    private $token;
    
    // token che il server ha inviato nell'ultima chiamata
    private $tokenServer;
    
    /**
     * La chiave generata si affida ad un servizio esterno per la sua memorizzazione
     *  e gestione del recupero. Essa è unica 
     * @staticvar type $instance
     * @param \CryptoChannel\IfcRestore $sourcer
     * @return \self
     */
    public static function getKey(RestoreInterface $sourcer = null)
    {
        if (!$sourcer) {
            return new self();
        }
        $instance = $sourcer->loadObject();
        if (!$instance) {
            $instance = new self($sourcer);
        } else {
            $instance->setSourcer($sourcer);
        }
        return $instance;
    }
    
    protected function __construct(RestoreInterface $sourcer = null)
    {
        $this->sourcer = $sourcer;
        
        if ($sourcer) {
            $sourcer->storeObject($this);
        }
        
    }
    
    public function setSourcer(RestoreInterface $sourcer = null)
    {
        $this->sourcer = $sourcer;
        return $this;
    }
    
    public function setPublic($public)
    {
        $this->pubKey = $public;
        if ($this->symKey) {
            $this->setSimmetric($this->symKey);
        }
        return $this;
    }
    
    public function getPublic()
    {
        return $this->pubKey;
    }
    
    public function getSimmetric()
    {
        return $this->symKey;
    }
    
    public function setSimmetric($key)
    {
        $this->symKey = $key;
        // crittazione chiave
        
        \openssl_public_encrypt($key, $cry_sym_bin, $this->getPublic());
        $this->cryKey = base64_encode($cry_sym_bin);
        
        // token di verifica invio chiave
        $this->token = $this->makeRandomString(4);
        
        // salvataggio stato
        if ($this->sourcer) {
            $this->sourcer->storeObject($this);
        }
        return $this;
    }
    
    /**
     * Generazione chiave simmetrica
     */
    private function makeRandomString($length)
    {
        $newKey = substr(
            str_shuffle(
                str_repeat(
                    $x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length/strlen($x))
                )
            ),
            1,
            $length
        );
        return $newKey;
    }
    /**
     * Crittazione dati da inviare al server
     *
     * @param string $data
     * @return string
     */
    public function encrypt($message)
    {
        if (!$this->getPublic()) {
            // non si può crittare nulla
            return $message;
        }
        
        $prefix = '0';
        
        if (!$this->getSimmetric()) {
            $this->setSimmetric($this->makeRandomString(100));
        }
        // se il token indicato è diverso da quello previsto allora 
        // la chiave viene reinviata al server
        if ($this->getToken() != $this->getServerToken()) {
            // ricalcolo del prefix
            $len = \dechex(\strlen($this->cryKey));
            $prefix = strlen($len) . $len . $this->cryKey;
        }
        return $prefix . Util::encrypt($message, $this->getSimmetric());
    }

    /**
     * Decrittazione dati provenienti dal server
     *
     * @param string $message
     * @return string
     */
    public function decrypt($message)
    {
        // messaggio decrittato con la chiave simmetrica
        $message_decrypt = Util::decrypt($message, $this->getSimmetric());
        return $message_decrypt;
    }
    
    public function getToken()
    {
        return $this->token;        
    }
    
    public function setServerToken($token)
    {
        $this->tokenServer = $token;
        // salvataggio stato
        if ($this->sourcer) {
            $this->sourcer->storeObject($this);
        }
    }
    
    public function getServerToken()
    {
        return $this->tokenServer;
    }
}

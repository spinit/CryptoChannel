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
    }
    
    public function setPublic($public)
    {
        return $this->pubKey = $public;
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
        if ($this->sourcer) {
            $this->sourcer->storeObject($this);
        }
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
            // generazione/crittazione/trasmissione chiave simmetrica
            $length = 100;
            $this->setSimmetric( 
                substr(
                    str_shuffle(
                        str_repeat(
                            $x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                            ceil($length/strlen($x))
                        )
                    ),
                    1,
                    $length
                )
            );
            // crittazione chiave
            \openssl_public_encrypt($this->getSimmetric(), $cry_sym_bin, $this->getPublic());
            $cry_sym = base64_encode($cry_sym_bin);
            
            // ricalcolo del prefix
            $len = \dechex(\strlen($cry_sym));
            $prefix = strlen($len) . $len . $cry_sym;
        }
        $message = Util::encrypt($message, $this->symKey);
        return $prefix . $message;
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
}

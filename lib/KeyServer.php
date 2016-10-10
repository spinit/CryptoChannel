<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;

/**
 * Description of KeyData
 *
 * @author ermanno
 */
class KeyServer
{
    // seme chiave privata / pubblica
    private $key;
    
    // chiave privata
    private $priKey;
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
        
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $this->key = \openssl_pkey_new($config);
        // Extract the private key from $res to $privKey
        \openssl_pkey_export($this->key, $this->priKey, "phrase", $config);
        $pubKey = \openssl_pkey_get_details($this->key);
        $this->pubKey = $pubKey["key"];
        
        if ($this->sourcer) {
            $this->sourcer->storeObject($this);
        }
    }
    
    public function setSourcer(RestoreInterface $sourcer = null)
    {
        $this->sourcer = $sourcer;
    }
    public function getPrivate()
    {
        return $this->priKey;
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
     * Crittazione dati da inviare al client
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $message = AesCtr::encrypt($data, $this->symKey, 256);
        return $message;
    }

    /**
     * Decrittazione dati provenienti dal client
     *
     * @param string $message
     * @return string
     */
    public function decrypt($message, $private = true)
    {
        // il primo carattere indica la lunghezza del contatore di lunghezza della chiave
        $lenlen = substr($message, 1, $message{0});
        $len = 0;
        if (strlen($lenlen)) {
            // è stata fornita la chiave ... quindi occorre decrittarla
            $len = \hexdec($lenlen);
            // chiave simmetrica crittata
            $sym_key_cry = base64_decode(substr($message, strlen($lenlen) + 1, $len));
            // chiave simmetrica decrittata con la chiave privata
            \openssl_private_decrypt($sym_key_cry, $sym_key, openssl_pkey_get_private($this->getPrivate(),'phrase'));
            $this->setSimmetric($sym_key);
        }
        //messaggio crittato
        $ciphertext = substr($message, $len + strlen($lenlen) + 1);
        // messaggio decrittato con la chiave simmetrica
        $message_decrypt = AesCtr::decrypt($ciphertext, $this->getSimmetric(), 256);
        return $message_decrypt;
    }
}

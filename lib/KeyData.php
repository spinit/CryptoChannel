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
class KeyData
{
    // seme chiave privata / pubblica
    private $key;
    // chiave privata
    private $priKey;
    // chiave pubblica
    private $pubKey;
    // chiave simmetrica
    private $symKey;
    
    public static function getInstance(InterfaceSource $sourcer = false)
    {
        static $instance = false;
        
        if ($instance) {
            return $instance;
        }
        
        if (!$sourcer) {
            $sourcer = new KeySourcerStandard('_[keys]');
        }
        
        // la chiave era stata memorizzata
        if ($keyStream = $sourcer->loadKey()) {
            $instance = unserialize($keyStream);
        } else {
            $instance = new self();
        }
        return $instance;
    }
    
    protected function __construct()
    {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $this->key = \openssl_pkey_new($config);
        // Extract the private key from $res to $privKey
        \openssl_pkey_export($this->key, $this->priKey);
        $pubKey = \openssl_pkey_get_details($this->key);
        $this->pubKey = $pubKey["key"];
    }
    
    public function getPrivate()
    {
        return $this->priKey;
    }
    public function getPubblic()
    {
        return $this->pubKey;
    }
    public function getSimmetric()
    {
        return $this->symKey;
    }
    public function getSimmetric($key)
    {
        $this->symKey = $key;
        $this->store();
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;

/**
 * Description of KeyServer
 *
 * @author ermanno.astolfi@spinit.it
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
    // token di verifica
    private $token;
    
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
        $instance = false;
        // primo tentativo ... caricamento dal sorgente indicato
        if ($sourcer) {
            $instance = $sourcer->loadObject();
        }
        // secondo tentavito ... caricamento dal sorgente di default
        if (!$instance) {
            $instance = self::getDefaultKey();
        }
        if (!$instance) {
            // terzo tentativo generazione nuova chiave
            $instance = new self($sourcer);
        } else {
            // oppure impostazione della sorgente indicata
            $instance->setSourcer($sourcer);
        }
        return $instance;
    }
    
    private static function getDefaultKey($countDown = 10)
    {
        $keyFile = __DIR__.'/../.htServerKey';
        $strKey = trim(@file_get_contents($keyFile));
        if ($strKey != '') {
            if ($strKey == 'WAIT' and $countDown>0) {
                sleep(1);
                return self::getDefaultKey($countDown -1);
            }
            if ($strKey != 'WAIT' and (filemtime($keyFile) + 3600 > time()) ) {
                // se la chiave ha menu di un'ora ... viene presa
                // altrimenti ne viene generata una nuova
                return unserialize($strKey);
            }
        }
        @file_put_contents($keyFile, "WAIT");
        $key = new self();
        @file_put_contents($keyFile, serialize($key));
        return $key;
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
    public function getToken()
    {
        return $this->token;
    }
    public function setSimmetric($key, $token)
    {
        //var_dump($key, $token);
        $this->symKey = $key;
        $this->token = $token;
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
        if (!$this->symKey) {
            return $data;
        }
        $message = Util::encrypt($data, $this->symKey);
        return $message;
    }

    /**
     * Decrittazione dati provenienti dal client
     *
     * @param string $message
     * @return string
     */
    public function decrypt($message, $token)
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
            \openssl_private_decrypt($sym_key_cry, $sym_key, openssl_pkey_get_private($this->getPrivate(), 'phrase'));
            if (!$sym_key) {
                //var_dump(substr($message, strlen($lenlen) + 1, $len), $this->getPublic(), $this->getPrivate());exit;
                //var_dump($sym_key, $this->getPublic(), $message);exit;
            }
            $this->setSimmetric($sym_key, $token);
        }
        if ($this->token != $token or !$this->getSimmetric()) {
            throw new ChannelException("Token mismatch : {$this->token} - {$token} - {$this->symKey}");
        }
        //messaggio crittato
        $ciphertext = substr($message, $len + strlen($lenlen) + 1);
        // messaggio decrittato con la chiave simmetrica
        $message_decrypt = Util::decrypt($ciphertext, $this->getSimmetric());
        return $message_decrypt;
    }
}

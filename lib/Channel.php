<?php
namespace CryptoChannel;

class Channel
{
    private $key = false;
    public function getKey()
    {
        return $this->key;
    }
    public function __construct()
    {
        session_start();
        $decrypted=NULL;


        if (empty($_SESSION['key']) || !empty($_GET['createNew'])) {

            // If no key pair is found, or the generation of a new one is request
            // create one and store it in the session -> this is unsecure and only meant for demonstration purposes !!!
            $this->key = \RSA\KeyPair::createNew();
            $_SESSION['key'] = serialize($this->key);
        } else {

            // If we have a key pair, load it
            $this->key = unserialize($_SESSION['key']);
        }
    }
    public function initJavascript($routeCrypto, $nameVar='CryptoChannel')
    {
        return "<script src='{$routeCrypto}?name={$nameVar}'></script>";
    }
}

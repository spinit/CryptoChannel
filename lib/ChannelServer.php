<?php
namespace CryptoChannel;
/**
 * Classe principale attraverso la quale crittare/decrittare i dati
 */
class ChannelServer
{
    private $key = false;
    
    /**
     * Restituisce l'inseme delle chiavi usate per la comunicazione
     * @return CryptoChannel\KeyData
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     *  Recupera l'insieme delle chiavi dal $wallet specificato.
     * 
     * Se non viene fornito un'altra fonte dati da cui recuperare la chiave usa
     * se stesso per memorizzare la chiave nella sessione.
     * @param type $wallet
     */
    public function __construct(RestoreInterface $wallet = null)
    {
        if (!$wallet) {
            $wallet = new RestoreSession(array('_','key','server'));
        }
        $this->key = KeyServer::getKey($wallet);
    }
    
    /**
     * Genera il codice javascript da utilizzare sul browser per permettere
     * la comunicazione crittata browser4server
     * 
     * @param string $nameVar nome della libreria da voler utilizzare sul browser
     */
    public function initJavascript($nameVar='CryptoChannel')
    {
        $pubKey = str_replace("\n","\\\n",$this->key->getPublic());
        //$prikey = str_replace("\n","\\\n",$this->key->getPrivate());
        
        header('Content-Type: application/javascript');
        $root = dirname(__DIR__);
        $script = '';
        $script .= file_get_contents($root.'/js/jsencrypt.min.js')."\n\n";
        $script .= file_get_contents($root.'/js/base64.js')."\n\n";
        $script .= file_get_contents($root.'/js/utf8.js')."\n\n";
        $script .= file_get_contents($root.'/js/aes.js')."\n\n";
        $script .= file_get_contents($root.'/js/aes-ctr.js')."\n\n";
        $script .= str_replace(array('{{nameVar}}', '{{pubKey}}'),
                               array($nameVar, $pubKey),
                               file_get_contents($root.'/js/cryptochannel.tjs'));
        return $script;
    }
    
    /**
     * Decrittazione dati
     * @param type $message
     * @return string
     */
    public function unpack($message)
    {
        if (@$_SERVER['HTTP_CRYPTION_TYPE'] == 'CryptoChannel') {
            return $this->key->decrypt($message);
        }
        return $message;
    }
    
    public function pack($message)
    {
        if (@$_SERVER['HTTP_CRYPTION_TYPE'] == 'CryptoChannel') {
            header('Cryption-Type: CryptoChannel');
            return $this->key->encrypt($message);
        }
        return $message;
    }
}

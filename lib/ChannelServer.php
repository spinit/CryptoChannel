<?php
namespace CryptoChannel;
/**
 * Classe principale attraverso la quale crittare/decrittare i dati
 */
class ChannelServer
{
    /**
     *
     * @var CryptoChannel\KeyServer
     */
    private $key = false;
    
    /**
     * Restituisce l'inseme delle chiavi usate per la comunicazione
     * @return CryptoChannel\KeyData
     */
    public function getKey()
    {
        return KeyServer::getKey($this->wallet);
    }
    
    public function isCallType($type)
    {
        return @$_SERVER['HTTP_CRYPTOCHANNEL_TYPE'] == $type;
    }
    public function getCallType()
    {
        return @$_SERVER['HTTP_CRYPTOCHANNEL_TYPE'];
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
            $wallet = new RestoreSession(array('_','crypto-channel','server'));
        }
        $this->wallet = $wallet;
    }
    
    /**
     * Genera il codice javascript da utilizzare sul browser per permettere
     * la comunicazione crittata browser4server
     * 
     * @param string $nameVar nome della libreria da voler utilizzare sul browser
     */
    public function initJavascript($nameVar='ChannelClient')
    {
        $pubKey = str_replace("\n","\\\n",$this->getKey()->getPublic());
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
            try {
                return $this->getKey()->decrypt($message, @$_SERVER['HTTP_CRYPTOCHANNEL_TOKEN']);
            } catch (ChannelException $e) {
                header('CryptoChannel-Status: ERROR');
                header('CryptoChannel-Message: '.$e->getMessage());
                throw new \Exception($this->getKey()->getPublic());
            }
        }
        return $message;
    }
    
    public function pack($message)
    {
        if (@$_SERVER['HTTP_CRYPTION_TYPE'] == 'CryptoChannel') {
            $cripted = $this->encrypt($message);
            header('Cryption-Type: CryptoChannel');
            return $cripted;
            
        }
        return $message;
    }
    public function encrypt($message)
    {
        $key = $this->getKey();
        return $key->getToken().$key->encrypt($message);
    }
}

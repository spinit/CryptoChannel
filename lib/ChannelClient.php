<?php
namespace CryptoChannel;
/**
 * Classe principale attraverso la quale crittare/decrittare i dati
 */
class ChannelClient
{
    private $key = false;
    
    private $callType = '';
    
    private $returnDataCrypted = 1;
    
    private $keyPublicUrl = '';
    
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
            $wallet = new RestoreSession(array('_','key','client'));
        }
        $this->cookie = new RestoreSession(array('_','key','cookie'));
        $this->key = KeyClient::getKey($wallet);
        $this->enableCryption();
    }
    
    /**
     * Decrittazione dati
     * @param type $message
     * @return string
     */
    public function setPublicUrl($url)
    {
        $this->keyPublicUrl = $url;
    }
    
    public function enableCryption($enable=1)
    {
        $this->returnDataCrypted = $enable;
    }
    
    /**
     * Richiede i dati al server inizializzando, se serve, la comunicazione 
     * @param string $url
     * @param array $data
     * @return type
     */
    public function getContent($url, $data)
    {
        if (!$this->key->getPublic()) {
            $pKey = $this->send($this->keyPublicUrl, '', array('crypting' => false));
            $this->key->setPublic($pKey);
        }
        $content = $this->send($url, $data, array('crypting'=>$this->returnDataCrypted));
        return $content;
    }
    public function setCallType($type)
    {
        $this->callType = $type;
    }
    /**
     * Invia i dati ad un servizio esterno
     * @param type $data
     */
    private function send($url, $data='', $option = array())
    {
        
        $cookies = $this->cookie->loadObject();
        $channelOption = new ChannelOption($option, $cookies, $this->getKey());
        $channelOption->setCallType($this->callType);
        
        $opts = array(
          'http'=>array(
            'method'    => $channelOption->getMethod($option),
            'header'    => $channelOption->getHeader($option),
                           // preparazione dati da inviare
            'content'   => $channelOption->sendData($data)
          )
        );
        
        $context = stream_context_create($opts);
        // invio dati
        $content = file_get_contents($url, false, $context);
        $this->debug = substr($content,0,5);
        // analisi della risposta
        $response = $channelOption->parseResponse($http_response_header, $content);
        if ($channelOption->getStatus() == 'ERROR') {
            if (!isset($option['stop-reload'])) {
                // rilettura della chiave pubblica
                $pKey = $this->send($this->keyPublicUrl, '', array('crypting' => false));
                $this->key->setPublic($pKey);
                $option['stop-reload'] = 1;
                return $this->send($url, $data, $option);
            }
            return false;
        }
        // memorizzazione nuovo stato
        $this->cookie->storeObject($channelOption->getCookie());
        
        // ritorno dati 
        return $response;
    }
}

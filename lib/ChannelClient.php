<?php
namespace CryptoChannel;
/**
 * Classe principale attraverso la quale crittare/decrittare i dati
 */
class ChannelClient
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
    
    public function getContent($url, $data)
    {
        if (!$this->key->getPublic()) {
            $pKey = $this->send($this->keyPublicUrl);
            $this->key->setPublic($pKey);
        }
        return $this->send($url, $data, array('crypting'=>$this->returnDataCrypted));
    }
    /**
     * Invia i dati ad un servizio esterno
     * @param type $data
     */
    private function send($url, $data='', $option = array())
    {
        
        $cookies = $this->cookie->loadObject();
        $channelOption = new ChannelOption($option, $cookies);
        if ($channelOption->isCrypting()) {
            $data = $this->getKey()->encrypt($data);
        }
        switch($channelOption->getType()) {
            case 'json' : 
                if (is_array($data)) {
                    $data = \json_encode($data);
                }
                break;
            case 'html':
            case 'xml':
            case 'plain':
                if (is_array($data)) {
                    $data = http_build_query($data);
                }
                break;
        }
        $opts = array(
          'http'=>array(
            'method'    => $channelOption->getMethod($option),
            'header'    => $channelOption->getHeader($option),
            'content'   => $data
          )
        );
        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $content = file_get_contents($url, false, $context);
        foreach($http_response_header as $s) {
            if(preg_match('|^Set-Cookie:\s*([^=]+)=([^;]+);(.+)$|', $s, $parts)) {
                $cookies[$parts[1]] = $parts[2];
            }
        }
        $content.="\n".json_encode($cookies);
        $this->cookie->storeObject($cookies);
        if ($channelOption->isCrypting()) {
            $content = $this->getKey()->encrypt($content);
        }
        return $content;
    }
}

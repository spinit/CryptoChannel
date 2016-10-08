<?php
namespace CryptoChannel;

class Channel implements IfcRestore
{
    private $key = false;
    
    public function getKey()
    {
        return $this->key;
    }
    public function loadObject()
    {
        if (!@$_SESSION['_']['key']) {
            return null;
        }
        return unserialize($_SESSION['_']['key']);
    }
    public function storeObject($data)
    {
        $_SESSION['_']['key'] = serialize($data);
    }
    public function __construct(IfcRestore $wallet = null)
    {
        if (!$wallet) {
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            $wallet = $this;
        }
        $this->key = KeyData::getKey($wallet);
    }
    public function initJavascript($nameVar='CryptoChannel')
    {
        $pubkey = str_replace("\n","\\\n",$this->key->getPublic());
        //$prikey = str_replace("\n","\\\n",$this->key->getPrivate());
        
        header('Content-Type: application/javascript');
        $root = dirname(__DIR__);
        $script = '';
        $script .= file_get_contents($root.'/js/jsencrypt.min.js')."\n\n";
        $script .= file_get_contents($root.'/js/base64.js')."\n\n";
        $script .= file_get_contents($root.'/js/utf8.js')."\n\n";
        $script .= file_get_contents($root.'/js/aes.js')."\n\n";
        $script .= file_get_contents($root.'/js/aes-ctr.js')."\n\n";
        $script .= <<<JS_END
                
{$nameVar} = new (function(){

    var pubkey = "{$pubkey}";

    function randomString(length) {
        return Math.round((Math.pow(36, length + 1) - Math.random() * Math.pow(36, length))).toString(36).slice(1);
    }

    var key_message = false; 
    var key_crypted = false;
    var cryptionEnable = true;
    
    function doAjax(url, data, callback)
    {

        function encrypt_message(plaintext)
        {
            var prefix = '0';
            // quando la chiave viene generata ... viene messa nel messaggio
            if (!key_message) {
                key_message = randomString(150);
                console.log(key_message);
    
                var encrypter = new JSEncrypt();
                encrypter.setPublicKey(pubkey);
                key_crypted = encrypter.encrypt(key_message); 

                var hexlen = Number(key_crypted.length).toString(16);

                prefix = hexlen.length + hexlen + key_crypted;
            }
    
            // crittazione simmetrica
            var encryptedMessage = Aes.Ctr.encrypt(plaintext, key_message, 256);
            console.log([plaintext, prefix]);
            // and concatenate our payload message
            var encrypted = prefix + encryptedMessage;

            return encrypted;
        }

        function decrypt_message(data)
        {
            var response = Aes.Ctr.decrypt(data, key_message, 256);
            return response;
        }

        data = data || {};
        if (typeof data == typeof {}) {
            var query = [];
            for (var key in data)
            {
                query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
            data = query.join('&');
        }
        var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                if (xmlhttp.getResponseHeader('Cryption-Type') == 'CryptoChannel') {
                    callback(decrypt_message(xmlhttp.responseText));
                } else {
                    callback(xmlhttp.responseText);
                }
            }
        }

        xmlhttp.open("POST", url, true);
    
        if (cryptionEnable) {
            xmlhttp.setRequestHeader("Cryption-Type","CryptoChannel");
            xmlhttp.send(encrypt_message(data));
        } else {
            xmlhttp.send(data);
        }
    }        

    this.send = doAjax;
    this.setCryption = function(a) {
        cryptionEnable = a;
    }
})();
JS_END;
        echo $script;
    }
    
    public function decrypt($message)
    {
        return $this->key->decrypt($message);
    }
    private function getSimmetricKey()
    {
        return @$_SESSION['_']['sym'];
    }
    private function setSimmetricKey($key)
    {
        $_SESSION['_']['sym'] = $key;
    }
    public function encrypt($data)
    {
        return $this->key->encrypt($data);
    }
}

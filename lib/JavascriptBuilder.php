<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;

/**
 * Description of JavascriptBuilder
 *
 * @author ermanno
 */
class JavascriptBuilder
{
    private $channel;
    
    private function __construct()
    {
        $this->channel = new Channel();
    }
    static function menage()
    {
        $builder = self::getInstance();
        if (isset($_GET['name'])) {
            return $builder->init($_GET['name']);
        }
        if (isset($_GET['pubkey'])) {
            return $builder->getPublicKey();
        }
    }
    static private function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }
    private function init($name)
    {
        header('Content-Type: application/javascript');
        $script = file_get_contents(__DIR__.'/../vendor/trenker/simple-rsa/javascript/rsa.min.js')."\n\n";
        $script .= file_get_contents(__DIR__.'/../js/base64.js')."\n\n";
        $script .= file_get_contents(__DIR__.'/../js/utf8.js')."\n\n";
        $script .= file_get_contents(__DIR__.'/../js/aes.js')."\n\n";
        $script .= file_get_contents(__DIR__.'/../js/aes-ctr.js')."\n\n";
        $private = file_get_contents(__DIR__.'/../js/private.js');
        $script .= <<<JS_END
                
{$name} = new (function(){

    {$this->channel->getKey()->toJavascript()}

    function randomString(length) {
        //return 'ermanno12ermanno';
        return Math.round((Math.pow(36, length + 1) - Math.random() * Math.pow(36, length))).toString(36).slice(1);
    }

    var routeUri = '{$_SERVER['REQUEST_URI']}'.split('?').shift();
    
    var key_message = false; 
    var key_crypted = false;
    function doAjax(url, data, callback)
    {

        function encrypt_message(plaintext)
        {
            var prefix = '0';
            // quando la chiave viene generata ... viene messa nel messaggio
            if (!key_message) {
                key_message = randomString(150);; 
                key_crypted = rsaEncrypter.encrypt(key_message); 
    
                var hexlen = Number(key_crypted.length).toString(16);

                prefix = hexlen.length + hexlen + key_crypted;
            }
    
            // crittazione simmetrica
            var encryptedMessage = Aes.Ctr.encrypt(plaintext, key_message, 256);
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
        var crypted = encrypt_message(data);
    
        var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                callback(decrypt_message(xmlhttp.responseText));
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.send(crypted);
    }        

    this.send = doAjax;
})();
JS_END;
        echo $script;
    }
    
    private function getPublicKey()
    {
        return 'hello world';
    }
}

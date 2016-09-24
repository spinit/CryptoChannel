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
        $script = file_get_contents(__DIR__.'/../vendor/trenker/simple-rsa/javascript/rsa.min.js');
        $script .= <<<JS_END
                
{$name} = new (function(){
    {$this->channel->getKey()->toJavascript()}
    var routeUri = '{$_SERVER['REQUEST_URI']}'.split('?').shift();
    var pubKey = {'key' : ''};
    function doAjax(url, data, callback)
    {
        data = data || {};
        if (typeof data == typeof {}) {
            var query = [];
            for (var key in data)
            {
                query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
            data = query.join('&');
        }
    
        var crypted = rsaEncrypter.encrypt(data);
        var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                callback(xmlhttp.responseText);
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

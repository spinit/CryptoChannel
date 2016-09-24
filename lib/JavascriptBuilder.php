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
class JavascriptBuilder {
    //put your code here
    static function menage()
    {
        $script = <<<JSEND
        {$_GET['name']} = new (function(){
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
                var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        callback(xmlhttp.responseText);
                    }
                }

                xmlhttp.open("POST", url, true);
                xmlhttp.send(data);
            }        
            
            this.send = function(url, txt, fn) {
                fn(txt + ' hello');
            }
        })();
JSEND;
        echo $script;
    }
}

<?php
include(dirname(__DIR__).'/autoload.php');

// per decodificare la comunicazione in ingresso
use CryptoChannel\ChannelServer;

// per codificare la comunicazione in uscita
use CryptoChannel\ChannelClient;
use CryptoChannel\ChannelException;
use CryptoChannel\Util;

$channelServer = new ChannelServer();

// Il comando "echo" ritorna al client una stampa lievemente corretta di ciò che gli è stato inviato
// Assegnando ad "echo" un valore maggiore di zero verrà ritornato al client il valore calcolato da un altro server (se stesso) che riceverà come input quanto fino ad ora calcolato
// Tale server comunicherà attraverso un'altra connessione crittata con un altro insieme di chiavi.
if (isset($_GET['echo'])) {
    $content = file_get_contents("php://input");
    
    //Util::setLogFile('/tmp/cryptochannel.log');
    Util::log('echo '.@$_GET['echo'], substr($content,0,10));
    Util::log('sym key', $channelServer->getKey()->getSimmetric());
    
    try {
        
        $data = $channelServer->unpack($content);
        $message = "Ricevuto [{$data}]";
        
        if ($_GET['echo']>0) {
            list($base, ) = explode('?', $_SERVER['REQUEST_URI']);
            $base = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$base}";

            // chiamata ad altro server con altro insieme di chiavi
            $channelClient = new ChannelClient();
            $channelClient->setPublicUrl("{$base}?pubkey");
            $channelClient->setCallType('Client-'.$_GET['echo']);
            $channelClient->enableCryption(1);
            $message = $channelClient->getContent($base.'?echo='.($_GET['echo']-1), $message);
            header('CryptoChannel-Debug: '.$channelClient->debug.' '.@$_SERVER['HTTP_CRYPTOCHANNEL_DEBUG']);
            //$message .= '-'.$channelClient->getKey()->getSimmetric();
        }
        
        die($channelServer->pack($message));
        
    } catch (\Exception $ex) {
        die($ex->getMessage());
    }
}

// librerie javascript necessarie al browser
if (isset($_GET['initjs'])) {
    die($channelServer->initJavascript($_GET['initjs']));
}

// pubblicazione chiave pubblica
if (isset($_GET['pubkey'])) {
    die($channelServer->getKey()->getPublic());
}

// Interfaccia grafica
?>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="?initjs=Krypto"></script>
    </head>
    <script>
        function talk() {
            Krypto.setCryption(document.getElementById('cryptionFlag').checked);
            Krypto.setType('html');
            Krypto.send('?echo='+document.getElementById('echoNum').value, 
                        document.getElementById('message').value,
                        function(response){
                document.getElementById('response').innerHTML = response;
            });
        }
    </script>
    <body>
        <form>
            <div style="width:400px;">
                <textarea id="message" class="messaggio" style="width:100%" rows="5"></textarea>
                <br/>
                <span>Trasmissione cifrata</span>
                <input type="checkbox" value="1" id="cryptionFlag"/>
                <span>&nbsp;&nbsp;Salti</span>
                <input type="text" size="3" value="0" id="echoNum"/>
                <button type="button" onclick="talk()" style="float:right">Invia</button>
                <br/>
                <pre id="response"></pre>
            </div>
        </form>
   </body>
</html>

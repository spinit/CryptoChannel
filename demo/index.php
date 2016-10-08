<?php
include('../autoload.php');
use CryptoChannel\Channel;
$channel = new Channel();

if (isset($_GET['echo'])) {
    $data = file_get_contents("php://input");
    if (@$_SERVER['HTTP_CRYPTION_TYPE'] == 'CryptoChannel') {
        header('Cryption-Type: CryptoChannel');
        $data = $channel->decrypt($data);
    }
    $message = 'RICEVUTO : '.$data;

    if (@$_SERVER['HTTP_CRYPTION_TYPE'] == 'CryptoChannel') {
        $message = $channel->encrypt($message);
    }
    echo $message;
    exit;
}
if (isset($_GET['initjs'])) {
    $channel->initJavascript($_GET['initjs']);
    exit;
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="?initjs=Krypto"></script>
    </head>
    <script>
        function talk() {
            Krypto.setCryption(document.getElementById('cryptionFlag').checked);
            Krypto.send('?echo', document.getElementById('message').value, function(response){
                console.log(response);
                document.getElementById('response').innerHTML = response;
            });
        }
    </script>
    <body>
        <div style="width:400px;">
            <textarea id="message" style="width:100%" rows="5"></textarea>
            <br/>
            <span>Trasmissione cifrata</span>
            <input type="checkbox" value="1" id="cryptionFlag"/>
            <button type="button" onclick="talk()" style="float:right">Invia</button>
            <br/>
            <pre id="response"></pre>
        </div>
   </body>
</html>

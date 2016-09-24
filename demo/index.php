<?php
include('../vendor/autoload.php');
use CryptoChannel\Channel;
?>
<html>
    <head>
        <meta charset="UTF-8">

<?php
    $channel = new Channel();
    echo $channel->initJavascript('CryptoChannelRoute.php','Krypto');
?>
    </head>
    <script>
        function talk() {
            Krypto.send('echo.php', document.getElementById('message').value, function(response){
                console.log(response);
                document.getElementById('response').innerHTML = response;
            });
        }
    </script>
    <body>
        <textarea id="message" cols="50" rows="5"></textarea>
        <button type="button" onclick="talk()">Invia</button>
        <br/>
        <pre id="response"></pre>
   </body>
</html>

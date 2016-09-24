<?php
include('../vendor/autoload.php');
use CryptoChannel\Channel;
?>
<html>
    <head>
    <script src="//code.jquery.com/jquery-3.1.1.js"
            integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA="
            crossorigin="anonymous"></script>
<?php
    $channel = new Channel();
    echo $channel->initJavascript('KryptChannel', __DIR__);
?>
    </head>
    <script>
        function talk() {
            KryptChannel.send($('#message').val(), function(response){
                $('#response').html(response);
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

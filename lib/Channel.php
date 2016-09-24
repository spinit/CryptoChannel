<?php
namespace CryptoChannel;

class Channel
{
    public function initJavascript($nameJS, $dirRoot)
    {
        return "
        <script>
        {$nameJS} = {
            send : function(txt, fn) {
                fn(txt + '{$dirRoot}');
            }
        }
        </script>
        ";
    }
}

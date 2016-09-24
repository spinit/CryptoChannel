<?php
namespace CryptoChannel;

class Channel
{
    public function initJavascript($routeCrypto, $nameVar='CryptoChannel')
    {
        return "<script src='{$routeCrypto}?name={$nameVar}'></script>";
    }
}

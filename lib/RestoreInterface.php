<?php
namespace CryptoChannel;

interface RestoreInterface
{
    public function loadObject();
    public function storeObject($data);
}

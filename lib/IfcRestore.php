<?php
namespace CryptoChannel;

interface IfcRestore
{
    public function loadObject();
    public function storeObject($data);
}

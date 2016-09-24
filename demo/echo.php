<?php
include('../vendor/autoload.php');
use CryptoChannel\Channel;
$channel = new Channel();

$crypted = file_get_contents("php://input");
$data = $channel->getKey()->decrypt($crypted);

echo " ==> {$data}\n\n{$crypted}";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


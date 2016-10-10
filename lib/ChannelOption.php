<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CryptoChannel;

/**
 * Description of ChannelOption
 *
 * @author ermanno
 */

class ChannelOption 
{
    private $option;
    private $header;
    
    public function __construct($option, $cookies = array())
    {
        $header = "Accept-language: en\r\n";
        
        if (!is_array($option)) {
            $option = array();
        }
        if (!is_array($cookies)) {
            $cookies = array();
        }
        $option['method'] = isset($option['method']) ? $option['method']:'POST';
        $option['crypting'] = !isset($option['crypting']) or $option['crypting'];
        $option['type'] = isset($option['type']) ? $option['type']:'json';
        
        if (isset($option['headers'])){
            if (is_array($option['headers'])) {
                $header .= implode('\r\n',$option['headers']);
            } else {
                $header .= trim($option['headers']);
            }
            $header .= "\r\n";
        }
        switch($option['type']) {
            case 'json':
                $header .= "Content-Type: application/json\r\n";
                break;
            case 'html':
            case 'xml':
            case 'plain':
                $header .= "Content-Type: text/{$option['type']}; charset=UTF-8\r\n";
                break;
            default:
                $header .= "Content-Type: {$option['type']}\r\n";
        }
        if ($option['crypting']) {
            $header .= "Cryption-Type: CryptoChannel\r\n";
        }
        $str_cookies = '';
        foreach($cookies as $k=>$v) {
            $str_cookies .= $k.'='.$v.';';
        }
        $header .= "Cookie: {$str_cookies}\r\n";
                        
        $this->option = $option;
        $this->header = $header;
    }
    
    public function getMethod()
    {
        return $this->option['method'];
    }
    public function getHeader()
    {
        return $this->header;
    }
    public function isCrypting()
    {
        return $this->option['crypting'];
    }
    public function getType()
    {
        return $this->option['type'];
    }
}
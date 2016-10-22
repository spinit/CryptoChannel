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
    private $cookie;
    private $callType='';
    
    public function __construct($option = array(), $cookie = array())
    {
        
        if (!is_array($option)) {
            $option = array();
        }
        if (!is_array($cookie)) {
            $cookie = array();
        }
        
        $option['method'] = isset($option['method']) ? $option['method']:'POST';
        $option['crypting'] = (!isset($option['crypting']) or $option['crypting']);
        $option['type'] = isset($option['type']) ? $option['type']:'json';
                        
        $this->option = $option;
        $this->cookie = $cookie;
    }
    
    public function setCallType($type)
    {
        $this->callType = $type;
    }
    
    public function getMethod()
    {
        return $this->option['method'];
    }
    
    public function getHeader()
    {
        return "Accept-language: en\r\n".
            "CryptoChannel-Type: {$this->callType}\r\n".
            $this->getHeaderOption().
            $this->getHeaderContentType().
            $this->getHeaderCryptionType().
            $this->getHeaderCookie();
    }
    public function isCrypting()
    {
        return $this->option['crypting'];
    }
    public function getType()
    {
        return $this->option['type'];
    }
    
    /**
     * 
     * @return string
     */
    private function getHeaderOption()
    {
        $header = '';
        if (isset($this->option['headers'])) {
            if (is_array($this->option['headers'])) {
                $header .= implode('\r\n',$this->option['headers']);
            } else {
                $header .= trim($this->option['headers']);
            }
            $header .= "\r\n";
        }
        return $header;
    }
    private function getHeaderContentType()
    {
        $header = '';
        switch($this->option['type']) {
            case 'json':
                $header .= "Content-Type: application/json\r\n";
                break;
            case 'html':
            case 'xml':
            case 'plain':
                $header .= "Content-Type: text/{$this->option['type']}; charset=UTF-8\r\n";
                break;
            default:
                $header .= "Content-Type: {$this->option['type']}\r\n";
        }
        return $header;
    }
    private function getHeaderCryptionType()
    {
        $header = '';
        if ($this->option['crypting']) {
            $header .= "Cryption-Type: CryptoChannel\r\n";
        }
        return $header;
    }
    private function getHeaderCookie()
    {
        $header = '';
        $str_cookies = '';
        foreach($this->cookie as $k=>$v) {
            $str_cookies .= $k.'='.$v.';';
        }
        $header .= "Cookie: {$str_cookies}\r\n";
        return $header;
    }
}
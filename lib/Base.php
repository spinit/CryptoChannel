<?php
namespace CryptoChannel;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Base
{
    private static $instances = array();

    /**
     * Impostazione/Recupero oggetti globali
     * @param string $name
     * @return any
     */
    public function singleton($name)
    {
        $args = func_get_args();
        if(count($args)>1) {
            self::$instances[$name] = $args[1];
        }
        return @self::$instances[$name];
    }
    
    public function util()
    {
        $args = func_get_args();
        if (count($args)) {
            $this->singleton('util', $args[0]);
        }
        $util = $this->singleton('util');
        if (!$util) {
            $util = $this->singleton('util', new Util);
        }
        return $util;
    }
}
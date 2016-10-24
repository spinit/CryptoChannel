<?php
namespace CryptoChannel;


\date_default_timezone_set('Europe/Rome');

class Util
{
    private static $logFile = '';
    
    function autoload($class)
    {
        $root = __DIR__;
        
        $ns = __NAMESPACE__.'\\';
        if (substr($class,0,strlen($ns)) != $ns) {
            return;
        }
        
        $class = substr($class, strlen($ns));
        
        $list_class = explode(DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $class));
        $path_class = $root;
        foreach($list_class as $item) {
            $file_init = $path_class . DIRECTORY_SEPARATOR . '__init__.php';
            if (is_file($file_init)) {
                require_once($file_init);
            }
            $path_class .=  DIRECTORY_SEPARATOR . $item;
        }
        $path_class .= '.php';
        if (file_exists($path_class)) {
            require_once $path_class;
        }
    }
    static function setLogFile($file)
    {
        self::$logFile = $file;
    }
    static function log($title, $msg='')
    {
        if (!self::$logFile) {
            return;
        }
        $fp = fopen(self::$logFile,'a');
        if (!$fp) {
            return;
        }
        $root = realpath(__DIR__.'/..');
        $debug = debug_backtrace();
        $file = substr($debug[0]['file'], strlen($root));
        $line = $debug[0]['line'];
        $content = "== ".date("H:i:s")." == ".getmypid()." == {$file}:{$line} >> $title";
        if ($msg) {
            if (!is_string($msg)) {
                $msg = json_encode($msg);
            }
            $content .= "\n".$msg;
        }
        fwrite($fp, $content."\n");
        fclose($fp);
    }
    public static function encrypt($plaintext, $password, $salt='!kQm*fF3pXe1Kbm%9')
    {
        return AesCtr::encrypt($plaintext, $password, 256);
    } 
    public static function decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9')
    {
        return AesCtr::decrypt($encrypted, $password, 256);
    }

}

return new Util();

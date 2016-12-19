<?php

include(dirname(__DIR__).'/autoload.php');

// se il test è effettuato da travis allora non viene fatto partire il server httpd
if (!getenv('DEBIAN_FRONTEND')) {
    // Command that starts the built-in web server
    $command = sprintf(
        'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
        WEB_SERVER_HOST,
        WEB_SERVER_PORT,
        WEB_SERVER_DOCROOT
    );
    // Execute the command and store the process ID
    $output = array(); 
    exec($command, $output);
    echo $command;
    
    $pid = (int) $output[0];
    sleep(3);
    echo sprintf(
        '%s - Web server started on %s:%d with PID %d', 
        date('r'),
        WEB_SERVER_HOST, 
        WEB_SERVER_PORT, 
        $pid
    ) . PHP_EOL;

    // Kill the web server when the process ends
    register_shutdown_function(function() use ($pid) {
        echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
        exec('kill ' . $pid);
    });
}
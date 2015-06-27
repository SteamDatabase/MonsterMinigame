<?php
function SignalHandler( $Signal )
{
	l( 'Caught signal ' . $Signal );
	
	global $Server; // ayy

	$Server->Shutdown();
}

function l( $String )
{
    echo '[' . date( DATE_RSS ) . '] ' . $String . PHP_EOL;
}

/**
 * An example of a project-specific implementation.
 * 
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 * 
 *      new \Foo\Bar\Baz\Qux;
 *      
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'SteamDB\\CTowerAttack\\';
    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    // get the relative class name
    $relative_class = substr($class, $len);
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

$Server = new \SteamDB\CTowerAttack\Server( 5337 );

if( function_exists( 'pcntl_signal' ) )
{
	$Server->SaneServer = true;

	pcntl_signal( SIGTERM, 'SignalHandler' );
	pcntl_signal( SIGINT, 'SignalHandler' );
}

$Server->Listen();

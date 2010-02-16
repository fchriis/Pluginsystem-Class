<?php
	error_reporting(E_ALL);
	
	// require plugin class
	require_once('Plugin.class.php');
	
	// load plugins
	Plugin::loadPlugins();
	
	// get plugin
	$p = new Plugin('testplugin');
	var_dump($p);
	
	$foo = "foo";
	
	// execute all eventHandlers
	// eventName: test
	// parameters: $foo as reference
	Plugin::fireEvent('test', array('foo' => &$foo));
	
	echo $foo;
?>

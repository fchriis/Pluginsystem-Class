<?php
	/*function test ($eventName, $parameters) {
		$parameters['foo'] = "bar";
	}*/
	
	//Plugin::addEventHandler('test', 'test');
	
	class TestPlugin {
		public static function foo ($eventName, $parameters) {
			$parameters["foo"] = "bar";
		}
	}
	
	Plugin::addEventHandler('test', array("TestPlugin", "foo"));
?>

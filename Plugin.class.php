<?php
	/**
	 * Mit dieser Klasse erweiterst du dein Projekt um eine Pluginunterstützung.
	 * Die Plugins liegen in Plugin::$DIR
	 * 
	 * Jedes Plugin ist ein Ordner mit einer plugin.txt, in dem
	 * Meta-Informationen im JSON-Format abgelegt sind.
	 * 
	 * Ein Plugin ist aktiv, wenn sich in seinem Ordner eine leere Textdatei mit
	 * dem Namen "active" befindet.
	 *
	 * Desweiteren braucht jedes
	 * Plugin eine plugin.php, die bei jedem Seitenaufruf aufgerufen wird. Dort
	 * kann man mittels Plugin::addEventHandler() seinen Code einbringen.
	 * 
	 * 
	 * With this class you can add a pluginengine to your project.
	 * Plugins are stored in Plugin::$DIR.
	 * 
	 * Every Plugin is a directory containing a plugin.txt where
	 * meta-information is stored in JSON-Format.
	 * 
	 * A Plugin is active if his directory is containing an empty file called "active".
	 * 
	 * Every plugin needs a plugin.php-file. This file is executed every request.
	 * You can append your code using Plugin::addEventHandler().
	 * 
	 * @author	Chris!
	 * @license	public domain
	 */
	class Plugin {
		public static $DIR;
		public static $loadedPlugins = array();
		public static $eventHandler = array();
		
		public $title = '';
		public $author = '';
		public $authorURL = '';
		public $description = '';
		public $version = '';
		
		/**
		 * Prüft ob ein Plugin installiert ist
		 * 
		 * checks if plugin is installed
		 * @param	string		$pluginName
		 * @return	boolean
		 */
		public static function isInstalled ($pluginName) {
			return file_exists(self::$DIR.$pluginName.'/plugin.txt');
		}
		
		/**
		 * Prüft ob ein Plugin aktiv ist
		 * 
		 * checks if plugin is active
		 * @param	string		$pluginName
		 * @return	boolean
		 */
		public static function isActive ($pluginName) {
			return file_exists(self::$DIR.$pluginName.'/active');
		}
		
		/**
		 * Prüfz ob ein Plugin geladen ist
		 * 
		 * checks if plugin is loaded
		 * @param	string		$pluginName
		 * @return	booelan
		 */
		public static function isLoaded ($pluginName) {
			return in_array($pluginName, self::$loadedPlugins);
		}
		
		/**
		 * Prüft ob der Pluginname valide ist
		 * 
		 * checks if pluginName is valid
		 * @param	string		$pluginName
		 * @return	boolean
		 */
		public static function isValidPluginName ($pluginName) {
			return preg_match('/([a-zA-Z-]+)([a-zA-Z0-9-]*)/', $pluginName);
		}
		
		public function __construct ($pluginName) {
			$this->pluginName = $pluginName;
			
			// prüfen ob Plugin vorhanden
			if (!Plugin::isInstalled($pluginName))
				throw new Exception ('Plugin not found');
			
			// Meta-Informationen laden
			$metaInfo = file_get_contents(self::$DIR.$pluginName.'/plugin.txt');
			$metaInfo = json_decode($metaInfo, true);
			
			$this->title = (!empty($metaInfo['title']) ? $metaInfo['title'] : '');
			$this->author = (!empty($metaInfo['author']) ? $metaInfo['author'] : '');
			$this->authorURL = (!empty($metaInfo['authorURL']) ? $metaInfo['authorURL'] : '');
			$this->description = (!empty($metaInfo['description']) ? $metaInfo['description'] : '');
			$this->version = (!empty($metaInfo['version']) ? $metaInfo['version'] : '');
		}
		
		/**
		 * Aktivert das Plugin
		 * 
		 * enables plugin
		 */
		public function enable () {
			if (!Plugin::isActive($this->pluginName))
				file_put_contents(self::$DIR.$this->pluginName.'/active', '');
		}
		
		/**
		 * Deaktiviert das Plugin
		 * 
		 * disables plugin
		 */
		public function disable () {
			if (Plugin::isActive($this->pluginName))
				unlink(self::$DIR.$this->pluginName.'/active');
		}
		
		/**
		 * Startet alle Plugins
		 * 
		 * loads all active plugins
		 */
		public static function loadPlugins () {
			$dir = opendir(self::$DIR);
			while ($file = readdir($dir)) {
				// prüfen ob Verzeichnis
				if (!is_dir(self::$DIR.$file)) continue;
				
				// prüfen ob Plugin
				if (!file_exists(self::$DIR.$file.'/plugin.txt')) continue;
				
				// prüfen ob Aktiv
				if (!file_exists(self::$DIR.$file.'/active')) continue;
				
				// prüfen ob plugin.php vorhanden und requiren
				if (file_exists(self::$DIR.$file.'/plugin.php')) {
					self::$loadedPlugins[] = $file;
					require_once(self::$DIR.$file.'/plugin.php');
				}
			}
			closedir($dir);
		}
		
		/**
		 * Fügt eine EventHandler hinzu
		 * 
		 * adds an eventHandler
		 * @param	string		$eventName
		 * @param	mixed		$eventHandler
		 */
		public static function addEventHandler ($eventName, $eventHandler) {
			if (!isset(self::$eventHandler[$eventName]))
				self::$eventHandler[$eventName] = array();
			
			self::$eventHandler[$eventName][] = $eventHandler;
		}
		
		/**
		 * Führt alle EventHandler zu einem Event aus
		 * Weitere Parameter können als Referenz übergeben werden
		 * 
		 * executes all eventHandlers
		 * additional parameters can be passed as reference
		 * @param	string		$eventName
		 * @param	array		$parameters
		 */
		public static function fireEvent ($eventName, $parameters = array()) {
			if (isset(self::$eventHandler[$eventName])) {
				foreach (self::$eventHandler[$eventName] as $eventHandler) {
					call_user_func_array($eventHandler, array($eventName, $parameters));
				}
			}
		}
	}
	
	Plugin::$DIR = dirname(__FILE__).'/plugins/';
?>

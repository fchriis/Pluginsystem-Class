Pluginsystem Class
==================

With this class you can add a pluginengine to your project.
Plugins are stored in Plugin::$DIR.

Every Plugin is a directory containing a plugin.txt where
meta-information is stored in JSON-Format.

A Plugin is active if his directory is containing an empty file called "active".

Every plugin needs a plugin.php-file. This file is executed every request.
You can append your code using Plugin::addEventHandler().

author: fchriis
license: public domain

Requirements
------------
- PHP 5.2

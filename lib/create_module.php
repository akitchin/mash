<?php
include_once('helpers.php');

// Standard module config XML template for Magento
define('BASE_XML', '<?xml version="1.0" encoding="UTF-8"?><config><modules></modules></config>');

// Entry point for script, passes optional command line parameters
createModule($argv[1], $argv[2]);

/**
 * Creates a module in Magento.
 *
 * @param $package - the package name. If not specified, the user is prompted it.
 * @param $module - module name. If not specified, the user is prompted for it.
 */
function createModule($package = FALSE, $module = FALSE) {
  if (!$package) {
    $package = in('What is the package name? The package will be created if it doesn\'t exist.');
  }
  if (!$module) {
    $module = in('What is the module name?');
  }

  // XML for the app/etc/modules/<Package>_<Module>.xml
  createModulesXml($package, $module);

  // XML for app/code/local/<Package>/<Module>/etc/config.xml
  createModuleXml($package, $module);
}

/**
 * Creates the XML file in app/etc/modules/
 *
 * @param $package - package name
 * @param $module - module name
 */
function createModulesXml($package, $module) {
  $package_module = $package . '_' . $module;
  $xml = new SimpleXMLElement(BASE_XML);
  $xml->modules->addChild($package_module);
  $xml->modules->$package_module->addChild('active', 'true');
  $xml->modules->$package_module->addChild('codePool', 'local');
}

/*
 * XML for app/code/local/<Package>/<Module>/etc/config.xml
 *
 * @param $package - package name
 * @param $module - module name
 */
function createModuleXml($package, $module) {
  $package_module = $package . '_' . $module;
  $xml = new SimpleXMLElement(BASE_XML);
  $xml->modules->addChild($package_module);
  $xml->modules->$package_module->addChild('version', '0.1.0');
}



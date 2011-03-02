<?php
include_once('helpers.php');
include_once('clear_cache.php');

// Entry point for script, passes optional command line parameters
createRoute($argv[1], $argv[2], $argv[3], $argv[4]);

/**
 * Creates a module in Magento.
 *
 * @param $package - the package name. If not specified, the user is prompted it.
 * @param $module - module name. If not specified, the user is prompted for it.
 * @param $area - the Magento Area, 'frontend', 'admin', or 'install'.
 * @param $frontName - Front name.
 */
function createRoute($package = FALSE, $module = FALSE, $area = FALSE, $frontName = FALSE) {
  if (!$package) {
    $package = in('What is the package name? The package will be created if it doesn\'t exist.');
  }
  if (!$module) {
    $module = in('What is the module name?');
  }
  if (!$area) {
    $area = in('What is the Magento Area? Options are "frontend", "admin", and "install"');
  }
  if (!$frontName) {
    $frontName = in('What is the Front Name? Often is the module name ("' . $module . '")');
  }

  // Verify the module already exists
  $configPath = getConfigPath($package, $module);
  if ($configPath) {
    $xml = simplexml_load_file($configPath);
    if ($xml) {
      $lowercaseModule = strtolower($module);

      // Adds the route
      $xml->addChild($area);
      $xml->$area->addChild('routers');
      $xml->$area->routers->addChild($lowercaseModule);
      $xml->$area->routers->$lowercaseModule->addChild('use', 'standard');
      $xml->$area->routers->$lowercaseModule->addChild('args');
      $xml->$area->routers->$lowercaseModule->args->addChild('module', $package . '_' . $module);
      $xml->$area->routers->$lowercaseModule->args->addChild('frontName', $frontName);
      $formattedXml = formatXml($xml);

      // Writes file
      $fileHandler = fopen($configPath, 'w');
      fwrite($fileHandler, $formattedXml);
      fclose($fileHandler);
    }

  }
  // The module does not exists, alerts user
  else {
    out('There is no module "' . $module . '" under package "' . $package . '". Use `mash create-module ' . $package . ' ' . $module . '` to create one.');
  }
}


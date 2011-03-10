<?php
include_once('helpers.php');
include_once('clear_cache.php');
include_once('Module.php');


class Route extends Module 
{
  protected $area;
  protected $frontName;

 /**
  * Creates a route in Magento.
  *
  * @param $module - The fully qualified module name. Example: Package_Module. 
  *   If not specified, the user is prompted for it.
  * @param $area - the Magento Area, 'frontend', 'admin', or 'install'.
  * @param $frontName - Front name.
  */
  public function createRoute($module = FALSE, $area = FALSE, $frontName = FALSE) {
    // Request missing parameters
    if (!$module) {
      $module = in('What is the fully qualified module name? Example: Package_Module.');
    }
    $this->area = $area;
    if (!$this->area) {
      $this->area = in('What is the Magento Area? Options are "frontend", "admin", and "install"');
    }
    $this->frontName = $frontName;
    if (!$this->frontName) {
      $this->frontName = in('What is the Front Name? The Front Name is often the lowercase module name.');
    }

    // Validates parameters
    $components = explode('_', $module);
    if (count($components) < 2) {
      error('Module name needs to be fully qualified. Example: Package_Module');
      return;
    }

    $this->package         = $components[0];
    $this->module          = $components[1];
    $this->lowercaseModule = strtolower($this->module);
    $this->packageModule   = $this->package . '_' . $this->module;

    // Verify the module already exists
    if (!$this->moduleExists()) {
      error('There is no module "' . $this->module . '" under package "' . $this->package . '". Use `mash create-module ' . $module . '` to create one.');
      return;
    }

    $this->createRouteConfig();

    clearCache();
  }

  /**
   * Adds the XML configuration for the block to config.xml.
   */
  protected function createRouteConfig() {
    $configPath = getConfigPath($this->package, $this->module);
    $xml = simplexml_load_file($configPath);

    if ($xml) {
      $area = $this->area;
      $lowercaseModule = $this->lowercaseModule;
      $blockShortname = $this->blockShortname;

      // Adds the area (frontend/admin/local)
      if (empty($xml->$area)) {
        $xml->addChild($area);
      }

      // Adds the route. Only adds if the route does not already exists (one 
      // route per area).
      if (empty($xml->$area->routers)) {
        $xml->$area->addChild('routers');
        $xml->$area->routers->addChild($lowercaseModule);
        $xml->$area->routers->$lowercaseModule->addChild('use', 'standard');
        $xml->$area->routers->$lowercaseModule->addChild('args');
        $xml->$area->routers->$lowercaseModule->args->addChild('module', $this->packageModule);
        $xml->$area->routers->$lowercaseModule->args->addChild('frontName', $this->frontName);
      }
      $formattedXml = formatXml($xml);

      // Writes file
      $fileHandler = fopen($configPath, 'w');
      fwrite($fileHandler, $formattedXml);
      fclose($fileHandler);
    }

  }

}


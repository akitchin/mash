<?php
include_once('helpers.php');
include_once('clear_cache.php');


class Module
{
  // Standard module config XML template for Magento
  const BASE_XML = '<?xml version="1.0" encoding="UTF-8"?><config><modules></modules></config>';

  protected $package;
  protected $module;
  protected $lowercaseModule;
  protected $packageModule;

  /**
  * Creates a module in Magento.
  *
  * @param $module - The fully qualified module name. Example: Package_Module. 
  *   If not specified, the user is prompted for it.
  */
  public function createModule($module) {
    // Request missing parameters
    if (!$module) {
      $module = in('What is the fully qualified module name? Example: Package_Module.');
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

    // XML for the app/etc/modules/<Package>_<Module>.xml
    $this->createModulesXml();

    // XML for app/code/local/<Package>/<Module>/etc/config.xml
    $this->createModuleXml();

    // Any time XML has changed, need to clear cache
    clearCache();
  }

  /**
   * @return TRUE if the module exist. Checks for config.xml.
   */
  public function moduleExists() {
    return getConfigPath($this->package, $this->module);
  }

  /**
  * Creates the XML file in app/etc/modules/
  */
  protected function createModulesXml() {
    // Creates XML
    $packageModule = $this->packageModule;
    $xml = new SimpleXMLElement(Module::BASE_XML);
    $xml->modules->addChild($this->packageModule);
    $xml->modules->$packageModule->addChild('active', 'true');
    $xml->modules->$packageModule->addChild('codePool', 'local');

    // Formats XML with proper indention
    $formattedXml = formatXml($xml);

    // Writes XML to app/etc/modules/<Package>_<Module>.xml
    $rootPath = getRootPath();
    $etcPath = $rootPath . 'app/etc/modules/';
    $fileHandler = fopen($etcPath . $this->packageModule . '.xml', 'w');
    fwrite($fileHandler, $formattedXml);
    fclose($fileHandler);
  }

  /*
  * XML for app/code/local/<Package>/<Module>/etc/config.xml
  */
  protected function createModuleXml() {
    // Creates XML
    $packageModule = $this->packageModule;
    $xml = new SimpleXMLElement(Module::BASE_XML);
    $xml->modules->addChild($packageModule);
    $xml->modules->$packageModule->addChild('version', '0.1.0');

    // Formats XML with proper indention
    $formattedXml = formatXml($xml);

    // Creates package and module directory if not already created
    $localPath = getPath('local');
    $modulePath = $localPath . $this->package . '/' . $this->module . '/etc/';
    if (!file_exists($modulePath)) {
      mkdir($modulePath, 0755, TRUE);
    }

    // Writes XML to app/code/local/<Package>/<Module>/etc/config.xml
    $fileHandler = fopen($modulePath . 'config.xml', 'w');
    fwrite($fileHandler, $formattedXml);
    fclose($fileHandler);
  }
}


<?php
include_once('helpers.php');
include_once('clear_cache.php');
include_once('Module.php');


class Block extends Module 
{
  protected $block;
  protected $blockPrefix;
  protected $blockShortname;
  protected $rewrite;

  /**
  * Creates a block in Magento.
  *
  * @param $block - Full block name, like Package_Adminhtml_Block_Customer_Grid. 
  *   If not specified, the user is prompted
  * @param $rewrite - The full name of the block being extended, like Mage_Adminhtml_Block_Customer_Grid. 
  *   If not specified the user is prompted. However, the user is not required 
  *   to rewrite a block and the default HTML base block will be extended.
  */
  public function createBlock($block = FALSE, $rewrite = FALSE) {
    // Request missing parameters
    $this->block = $block;
    if (!$this->block) {
      $this->block = in('What is the Block name? Example: Package_Adminhtml_Block_Customer_Grid.');
    }
    $this->rewrite = $rewrite;
    if (!$this->rewrite) {
      $this->rewrite = in('What block does this rewrite? Example: Mage_Adminhtml_Block_Customer_Grid. Leave blank to extend the base block (Mage_Core_Block_Template).');
    }

    // Validates parameters
    $components = explode('_', $this->block);
    if (count($components) < 5) {
      error('Block name needs to be fully qualified. Example: Package_Adminhtml_Block_Customer_Grid');
      return;
    }

    $this->package         = $components[0];
    $this->module          = $components[1];
    $this->lowercaseModule = strtolower($this->module);
    $this->packageModule   = $this->package . '_' . $this->module;
    $this->blockPrefix     = substr($block, 0, strpos($block, 'Block_') + strlen('Block'));
    $this->blockShortname  = substr($block, strpos($block, 'Block_') + strlen('Block_'));

    // Verify the module already exists
    if (!$this->moduleExists()) {
      error('There is no module "' . $this->module . '" under package "' . $this->package . '". Use `mash create-module ' . $this->package . '_' . $this->module . '` to create one.');
      return;
    }

    //$this->createBlockConfig();

    $this->createBlockClass();

    clearCache();
  }

  /**
   * Adds the XML configuration for the block to config.xml.
   */
  protected function createBlockConfig() {
    $configPath = getConfigPath($this->package, $this->module);
    $xml = simplexml_load_file($configPath);

    if ($xml) {
      $lowercaseModule         = $this->lowercaseModule;
      $lowercasePackageModule  = strtolower($this->packageModule);
      $blockShortname          = $this->blockShortname;
      $lowercaseBlockShortname = strtolower($blockShortname);

      if (empty($xml->global)) {
        $xml->addChild('global');
      }
      if (empty($xml->global->blocks)) {
        $xml->global->addChild('blocks');
      }

      if (empty($xml->global->blocks->$lowercasePackageModule)) {
        $xml->global->blocks->addChild($lowercasePackageModule);
        $xml->global->blocks->$lowercasePackageModule->addChild('class', $this->blockPrefix);
      }

      // If block to extend given, then rewrites that block
      if (!empty($this->rewrite)) {
        if (empty($xml->global->blocks->$lowercaseModule)) {
          $xml->global->blocks->addChild($lowercaseModule);
        }

        if (empty($xml->global->blocks->$lowercaseModule->rewrite->$blockShortname)) {
          $xml->global->blocks->$lowercaseModule->addChild('rewrite');
          $xml->global->blocks->$lowercaseModule->rewrite->addChild($lowercaseBlockShortname, $this->block);
        }
      }

      // Writes file
      $formattedXml = formatXml($xml);
      $fileHandler = fopen($configPath, 'w');
      fwrite($fileHandler, $formattedXml);
      fclose($fileHandler);
    }

  }

  /**
   * Creates the class file for the block.
   */
  protected function createBlockClass() {
    // Creates the block directory if does not already exist
    $localPath = getPath('local');
    $directoryPath = $localPath;
    $directories = explode('_', $this->block);
    for ($i = 0; $i < count($directories) - 1; $i++) {
      $directoryPath .= $directories[$i] . '/';
    }
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, TRUE);
    }

    // Only create the class file if it does not already exists. Do not want to 
    // overwrite an existing file.
    $file = $directoryPath . $directories[count($directories) - 1] . '.php';
    if (!file_exists($file)) {

      // If block to rewrite not specified, extends base html Block
      $extends = $this->rewrite;
      if (!$extends) {
        $extends = 'Mage_Core_Block_Template';
      }

      // Code template
      $code = <<<CODE
<?php

class $this->block extends $extends {


}

CODE;

      // Writes class file
      $fileHandler = fopen($file, 'w');
      fwrite($fileHandler, $code);
      fclose($fileHandler);
    }
  }

}


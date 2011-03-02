<?php

//////////////////////////////////////////////////////////////////////////////
// IO
//////////////////////////////////////////////////////////////////////////////

/**
 * Finds the root directory of the Magento project. Assumes script is ran either 
 * at the Magento root folder or a subdirectory.
 *
 * @return the root path of the Magento directory with trailing slash or FALSE 
 * if not in Magento directory.
 */
function getRootPath() {
  $rootPath = FALSE;

  // Iterates through each directory and that directory's parent looking for 
  // indicators of the Magento root directory
  $cwd = getcwd();
  while ($cwd != '/' && !$rootPath) {
    // Looks for the 'mage' file in the current directory
    if ($handle = opendir($cwd)) {
      while (false !== ($file = readdir($handle)) && !$rootPath) {
        if ($file == 'mage') {
          $rootPath = $cwd . '/';
        }
      }
    }

    // Goes up one level
    if (!$rootPath) {
      chdir('../');
      $cwd = getcwd();
    }
  }

  return $rootPath;
}

/**
 * Looks for a directory inside the Magento project.
 *
 * @param $directory - directory to look for.
 * @param $rootDirectory - where to start the directory search. If not 
 *   specified, uses Magento root
 * @return the absolute path to the directory with trailing slash or FALSE if 
 *   could not be found
 */
function getPath($directory, $rootPath = FALSE) {
  $path = FALSE;

  // If root path to search not specified, uses Magento's root
  if (!$rootPath) {
    $rootPath = getRootPath();
  }

  if ($rootPath) {
    // Iterates through each directory and subdirectory looking for a matching 
    // directory
    $directoryIterator = new RecursiveDirectoryIterator($rootPath);
    $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($iterator as $file) {
      if ($file->isDir() && $file->getFilename() == $directory) {
        $path = $file->getRealpath() . '/';
        break;
      }
    }
  }

  return $path;
}

/**
 * Looks for the package path under local.
 *
 * @param $package - the package path name
 * @return the path to the specified package directory under app/code/local or 
 *   FALSE if could not be found.
 */
function getPackagePath($package) {
  $path = FALSE;

  $localPath = getPath('local');
  if ($localPath) {
    $path = getPath($package, $localPath);
  }

  return $path;
}

/**
 * Looks for the module path under package.
 *
 * @param $package - the package path name
 * @param $module - the module path name
 * @return the path to the specified module directory under app/code/local/<package> 
 *   or FALSE if could not be found.
 */
function getModulePath($package, $module) {
  $path = FALSE;

  $packagePath = getPackagePath($package);
  if ($packagePath) {
    $path = getPath($module, $packagePath);
  }

  return $path;
}

/**
 * Looks for the <package>/<module>/etc/config.xml file.
 *
 * @param $package - the package path name
 * @param $module - the module path name
 * @return the path to and including the config.xml file or FALSE if could not 
 *   be found.
 */
function getConfigPath($package, $module) {
  $path = FALSE;

  $modulePath = getModulePath($package, $module);
  if ($modulePath) {
    $configPath = $modulePath . 'etc/config.xml';
    if (file_exists($configPath)) {
      $path = $configPath;
    }
  }

  return $path;
}

/**
 * Wrapper for outputting a message to a user via STDOUT.
 *
 * @param $message - The string to prompt with
 */
function out($message = '') {
  fwrite(STDOUT, $message . "\n");
}

/**
 * Wrapper for getting user input from STDIN.
 *
 * @param $message - if set, prompts the user with the message before waiting 
 * for input.
 * @return the user's input, trimmed
 */
function in($message = FALSE) {
  if ($message) {
    out($message);
  }

  return trim(fgets(STDIN));
}

function formatXml($xml) {
  // Loads the XSLT
  $xslDoc = new DOMDocument();
  $xslDoc->load(__DIR__ . "/format.xsl");

  // Loads the XML
  $xmlDoc = new DOMDocument();
  $xmlDoc->loadXML($xml->asXML());

  // Processes the XML
  $proc = new XSLTProcessor();
  $proc->importStylesheet($xslDoc);

  return $proc->transformToXML($xmlDoc);
}


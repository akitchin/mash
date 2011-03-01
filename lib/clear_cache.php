<?php
include_once('helpers.php');

// Entry point for script
clearCache();

/**
 * Clears Magento's cache
 */
function clearCache() {

  // Looks under var/cache
  $varPath = getPath('var');
  if ($varPath) {
    $cachePath = getPath('cache', $varPath);

    // Clears the cache directory
    if ($cachePath) {
      $command = "rm -rf " . $cachePath . "*";
      system($command);
    }
  }
}


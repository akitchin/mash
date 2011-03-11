
### Introduction

Mash is a command line shell and scripting interface for Magento.

The objective is to make common Magento tasks accessible via the command line.
The project is inspired by [drush](http://drupal.org/project/drush).

### Setup

Mash is a PHP shell script executing various included PHP files to perform a
given task. To run mash, download or clone it to a directory in your bash
include path. Or download it to a home directory and alias the mash file (like,
`alias mash=/Users/baalexander/Development/mash/mash`.

### Commands

Commands should be ran inside the Magento project directory (or a subdirectory
in the Magento project directory).

 * clear-cache (cc for short)
   Clears the Magento cache.

   Example: `mash cc`

 * create-module [package] [module]
   Creates a modules in app/code/local. Package and Module can be specified as
   an argument or be prompted for the names while running

   Example: `mash create-module MyPackage MyModule`

 * create-route [package] [module] [area] [frontname]
   Adds a route to the config.xml for the given module. Package, Module, the
   area (like frontend), and frontname can be specified as an argument or be
   prompted for the names while running

   Example: `mash create-route MyPackage MyModule frontend mymodule`

 * help
   List of commands for mash

   Example: `mash help`

### License (MIT)

Copyright (C) 2011 by Brandon Alexander

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


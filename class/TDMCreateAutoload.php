<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * tdmcreate module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         tdmcreate
 * @since           2.5.0
 * @author          Txmod Xoops http://www.txmodxoops.org
 * @version         $Id: TDMCreateAutoload.php 12258 2014-01-02 09:33:29Z timgno $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');

/**
 * @since 1.91
 */
// Autoload Function
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

function application_autoloader($class) {
	$classFilename = $class.'.php';
	$cacheFile = __DIR__ . '/cache/classpaths.cache';
	$pathCache = (file_exists($cacheFile)) ? unserialize(file_get_contents($cacheFile)) : array();
	if (!is_array($pathCache)) { $pathCache = array(); }
	
	if (array_key_exists($class, $pathCache)) {
		/* Load class using path from cache file (if the file still exists) */
		if (file_exists($pathCache[$class])) { require_once $pathCache[$class]; }

	} else {
		/* Determine the location of the file within the $class_root and, if found, load and cache it */
		$directories = new RecursiveDirectoryIterator(__DIR__);
		foreach(new RecursiveIteratorIterator($directories) as $file) {
			if ($file->getFilename() == $classFilename) {
				$fullPath = $file->getRealPath();
				$pathCache[$class] = $fullPath;						
				require_once $fullPath; 
				break;
			}
		}			
	}

	$serialized_paths = serialize($pathCache);
	if ($serialized_paths != $pathCache) { file_put_contents($cacheFile, serialize($pathCache)); }
}

spl_autoload_register('application_autoloader');
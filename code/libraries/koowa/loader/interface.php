<?php
/**
 * @version 	$Id$
 * @package		Koowa_Loader
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * KLoader Interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 */
interface KLoaderInterface
{
    /**
     * Registers this instance as an autoloader.
     *
     * @return void
     */
    public function register();

    /**
     * Get the class registry object
     *
     * @return object KLoaderRegistry
     */
    public function getRegistry();

    /**
     * Get a path from an file
     *
     * Function will check if the path is an alias and return the real file path
     *
     * @param  string $path The path
     * @return string The file path
     */
    public function getFile($path);

 	/**
     * Add a loader adapter
     *
     * @param object    A KLoaderAdapter
     * @return void
     */
    public function addAdapter(KLoaderAdapterInterface $adapter);

	/**
     * Get the registered adapters
     *
     * @return array
     */
    public function getAdapters();

    /**
     * Set an file path alias
     *
     * @param string  $alias    The alias
     * @param string  $path     The path
     */
    public function setAlias($alias, $path);

    /**
     * Get a list of path aliases
     *
     * @return array
     */
    public function getAliases();

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @param string  $basepath The basepath
     * @return boolean  Returns TRUE if the class could be loaded, otherwise returns FALSE.
     */
    public function loadClass($class, $basepath = null);

	/**
     * Load a class based on an identifier
     *
     * @param string|object $identifier The identifier or identifier object
     * @return boolean Returns TRUE if the identifier could be loaded, otherwise returns FALSE.
     */
    public function loadIdentifier($identifier);

    /**
     * Load a class based on a path
     *
     * @param string	$path The file path
     * @return boolean  Returns TRUE if the file could be loaded, otherwise returns FALSE.
     */
    public function loadFile($path);

    /**
     * Get the path based on a class name
     *
     * @param string $class    The class name
     * @param string $basepath The basepath
     * @return string|false   Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function findPath($class, $basepath = null);
}
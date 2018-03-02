<?php

/**
 * Copyright (c) 2018 Justin Kuenzel (jukusoft.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


/**
 * Classloader
 * User: Justin
 * Date: 01.03.2018
 * Time: 23:55
 */

class ClassLoader {

    public static $classlist = array();

    public static $loadedClasses = 0;

    /**
     * initialize classloader (called only once / request)
     */
    public static function init () {

        //register autoloader
        spl_autoload_register('cms_autoloader');

        if (!file_exists(ROOT . "cache")) {
            mkdir(ROOT . "cache");
        }

        if (!file_exists(ROOT . "cache/classloader/classlist.php")) {
            self::rebuildCache();
        }

        require(ROOT . "cache/classloader/classlist.php");
        self::$classlist = $classlist;

    }

    public static function rebuildCache () {

        require_once(ROOT . "system/core/classes/packages.php");

        if (file_exists(ROOT . "cache/classloader/classlist.php")) {
            @unlink(ROOT . "cache/classloader/classlist.php");
        }

        if (!file_exists(ROOT . "cache/classloader")) {
            mkdir(ROOT . "cache/classloader");
        }

        $packages = Packages::listPackages();

        $classlist = array();

        foreach ($packages as $path) {
            $path = ROOT . "system/packages/" . $path . "/";

            if (file_exists($path . "classloader.xml")) {
                $xml = simplexml_load_file($path . "classloader.xml");

                foreach ($xml->xpath("//class") as $classname) {
                    $classlist[(String) $classname] = $path . "classes/" . strtolower((String) $classname) . ".php";
                }
            }
        }

        $handle = fopen(ROOT . "cache/classloader/classlist.php", "w");

        fwrite($handle, "<" . "?" . "php\r\n\r\n");

        fwrite($handle, "$" . "classlist = array(\r\n");

        foreach ($classlist as $classname=>$classpath) {
            fwrite($handle, "\t'" . $classname . "' => \"" . $classpath . "\",\r\n");
        }

        fwrite($handle, ");\r\n\r\n");

        fwrite($handle, "?" . ">");

        fclose($handle);

    }

}

/**
 * autoload function
 */
function cms_autoloader ($classname) {

    ClassLoader::$loadedClasses++;

    if (isset(Classloader::$classlist[$classname])) {
        require(Classloader::$classlist[$classname]);
        return null;
    }

    if (file_exists(ROOT . "system/core/classes/" . strtolower($classname) . ".php")) {
        require(ROOT . "system/core/classes/" . strtolower($classname) . ".php");
        return null;
    } else if (file_exists(ROOT . "system/core/exception/" . strtolower($classname) . ".php")) {
		require(ROOT . "system/core/exception/" . strtolower($classname) . ".php");
		return null;
	} else if (file_exists(ROOT . "system/core/driver/" . strtolower($classname) . ".php")) {
		require(ROOT . "system/core/driver/" . strtolower($classname) . ".php");
		return null;
	}

    $array = explode("_", strtolower($classname));

    if (sizeOf($array) == 3) {

        if ($array[0] == "plugin") {
            if (file_exists(ROOT . "plugins/" . strtolower($array[1]) . "/classes/" . strtolower($array[2]) . ".php")) {
                require(ROOT . "plugins/" . strtolower($array[1]) . "/classes/" . strtolower($array[2]) . ".php");
            } else {
                echo "Could not load plugin-class " . $classname . "!";
            }
        } else {
            if (file_exists(ROOT . "system/libs/smarty/sysplugins/" . strtolower($classname) . "php")) {
                require ROOT . "system/libs/smarty/sysplugins/" . strtolower($classname) . ".php";
            } else if ($classname == "Smarty") {
                require("system/libs/smarty/Smarty.class.php");
            } else {
                //echo "Could not (plugin) load class " . $classname . "!";
            }
        }

    } else if (sizeOf($array) == 1) {

        if (file_exists(ROOT . "system/classes/" . strtolower($classname) . ".php")) {
            include ROOT . "system/classes/" . strtolower($classname) . ".php";
        } else if (file_exists(ROOT . "system/libs/smarty/sysplugins/" . strtolower($classname) . "php")) {
            require ROOT . "system/libs/smarty/sysplugins/" . strtolower($classname) . ".php";
        } else if ($classname == "Smarty") {
            require("system/libs/smarty/Smarty.class.php");
        } else {
            echo "Could not load class " . $classname . "!";
        }

    }

}


?>

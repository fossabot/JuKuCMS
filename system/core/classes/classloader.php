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

class ClassLoader {

    public static $classlist = array();

    public static $loadedClasses = 0;

    /**
     * initialize classloader (called only once / request)
     */
    public static function init () {

        //register autoloader
        spl_autoload_register('cms_autoloader');

        if (!file_exists(ROOT_PATH . "cache")) {
            mkdir(ROOT_PATH . "cache");
        }

        if (!file_exists(ROOT_PATH . "cache/classloader/classlist.php")) {
            self::rebuildCache();
        }

        require(ROOT_PATH . "cache/classloader/classlist.php");
        self::$classlist = $classlist;

    }

    public static function rebuildCache () {

        require_once(ROOT_PATH . "system/core/classes/packages.php");

        if (file_exists(ROOT_PATH . "cache/classloader/classlist.php")) {
            @unlink(ROOT_PATH . "cache/classloader/classlist.php");
        }

        if (!file_exists(ROOT_PATH . "cache/classloader")) {
            mkdir(ROOT_PATH . "cache/classloader");
        }

        $packages = Packages::listPackages();

        $classlist = array();

        foreach ($packages as $path) {
            $path = ROOT_PATH . "system/packages/" . $path . "/";

            if (file_exists($path . "classloader.xml")) {
                $xml = simplexml_load_file($path . "classloader.xml");

                foreach ($xml->xpath("//class") as $classname) {
                    $classlist[(String) $classname] = $path . "classes/" . strtolower((String) $classname) . ".php";
                }
            }
        }

        $handle = fopen(ROOT_PATH . "cache/classloader/classlist.php", "w");

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

    if (file_exists(ROOT_PATH . "system/core/classes/" . strtolower($classname) . ".php")) {
        require(ROOT_PATH . "system/core/classes/" . strtolower($classname) . ".php");
        return null;
    } else if (file_exists(ROOT_PATH . "system/core/exception/" . strtolower($classname) . ".php")) {
		require(ROOT_PATH . "system/core/exception/" . strtolower($classname) . ".php");
		return null;
	} else if (file_exists(ROOT_PATH . "system/core/driver/" . strtolower($classname) . ".php")) {
		require(ROOT_PATH . "system/core/driver/" . strtolower($classname) . ".php");
		return null;
	}

    $array = explode("_", strtolower($classname));

    if (sizeOf($array) == 3) {

        if ($array[0] == "plugin") {
            if (file_exists(ROOT_PATH . "plugins/" . strtolower($array[1]) . "/classes/" . strtolower($array[2]) . ".php")) {
                require(ROOT_PATH . "plugins/" . strtolower($array[1]) . "/classes/" . strtolower($array[2]) . ".php");
            } else {
                echo "Could not load plugin-class " . $classname . "!";
            }
        } else {
            if (file_exists(ROOT_PATH . "system/libs/smarty/sysplugins/" . strtolower($classname) . "php")) {
                require ROOT_PATH . "system/libs/smarty/sysplugins/" . strtolower($classname) . ".php";
            } else if ($classname == "Smarty") {
                require("system/libs/smarty/Smarty.class.php");
            } else {
                //echo "Could not (plugin) load class " . $classname . "!";
            }
        }

    } else if (sizeOf($array) == 1) {

        if (file_exists(ROOT_PATH . "system/classes/" . strtolower($classname) . ".php")) {
            include ROOT_PATH . "system/classes/" . strtolower($classname) . ".php";
        } else if (file_exists(ROOT_PATH . "system/libs/smarty/sysplugins/" . strtolower($classname) . "php")) {
            require ROOT_PATH . "system/libs/smarty/sysplugins/" . strtolower($classname) . ".php";
        } else if ($classname == "Smarty") {
            require("system/libs/smarty/Smarty.class.php");
        } else {
            echo "Could not load class " . $classname . "!";
        }

    }

}


?>

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

interface ICache {

    public function init ($config);

    /**
     * put session
     *
     * @param $area cache area
     * @param $key cache key
     * @param $value cache entry value, can also be an object
     * @param $ttl time to live of cache entry in seconds (optional)
     */
    public function put ($area, $key, $value, $ttl = 180 * 60);

    public function get ($area, $key);

    public function contains ($area, $key) : bool;

    public function clear ($area = "", $key = "");

}

?>
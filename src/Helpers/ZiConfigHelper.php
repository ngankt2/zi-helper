<?php

namespace ZiBase\Helpers;

use Symfony\Component\Yaml\Yaml;

class ZiConfigHelper
{
    /**
     * @param $yaml
     * @return false|mixed
     * required composer require symfony/yaml
     */
    static function yaml2array($yaml): mixed
    {
        return Yaml::parse($yaml);
    }

    /**
     * @param $array
     * @return string
     * required composer require symfony/yaml
     */
    static function array2yaml($array): string
    {
        return Yaml::dump($array);
    }

}

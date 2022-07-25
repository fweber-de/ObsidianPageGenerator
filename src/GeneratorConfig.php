<?php

namespace ObsPager;

use Symfony\Component\Yaml\Yaml;

/**
 * GeneratorConfig
 *
 * @author Florian Weber <git@fweber.info>
 */
class GeneratorConfig
{
    /**
     * @var string
     */
    public string $theme;

    /**
     * @param string $path
     * @return GeneratorConfig
     */
    public static function fromFile(string $path): GeneratorConfig
    {
        $_config = Yaml::parse(file_get_contents($path));

        $config = new GeneratorConfig();
        $config->theme = $_config['theme']['name'];

        return $config;
    }
}

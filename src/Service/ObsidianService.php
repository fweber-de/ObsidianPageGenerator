<?php

namespace ObsPager\Service;

use ObsPager\Exception\ObsidanException;
use ObsPager\ObsidianInfo;
use Parsedown;
use SplFileInfo;
use Twig\Environment;

/**
 * ObsidianService
 *
 * @author Florian Weber <git@fweber.info>
 */
class ObsidianService
{
    const OBS_CONFIG_DIR = '.obsidian';

    /**
     * @param string $sourceDir
     * @return ObsidianInfo
     * @throws ObsidanException
     */
    public static function getInfo(string $sourceDir): ObsidianInfo
    {
        $configFolder = $sourceDir.'/'.self::OBS_CONFIG_DIR;

        if(!is_dir($configFolder)) {
            throw new ObsidanException(sprintf('the folder %s does not contain valid obsidian content (%s directory is missing)', $sourceDir, self::OBS_CONFIG_DIR));
        }

        if(!file_exists($configFolder.'/app.json')) {
            throw new ObsidanException(sprintf('the folder %s does not contain valid obsidian content (%s file is missing)', $configFolder, 'app.json'));
        }

        $_appConfig = json_decode(file_get_contents($configFolder.'/app.json'));

        $info = new ObsidianInfo();
        $info->attachmentFolderPath = $_appConfig->attachmentFolderPath;
        $info->workspaceName = basename($sourceDir);

        return $info;
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    public static function parse(SplFileInfo $file): string
    {
        $md = new Parsedown();

        $content = file_get_contents($file->getRealPath());
        $parsed = $md->parse($content);

        return $parsed;
    }

    public static function dump(Environment $twig, string $targetDir, SplFileInfo $file, string $parsed)
    {
        if(!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $t = $twig->render('page.html.twig', [
            'parsed' => $parsed,
        ]);

        file_put_contents($targetDir.'/'.$file->getBasename().'.html', $t);
    }
}

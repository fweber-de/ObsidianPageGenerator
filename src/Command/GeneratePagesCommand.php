<?php

namespace ObsPager\Command;

use http\Env;
use ObsPager\Exception\ObsidanException;
use ObsPager\GeneratorConfig;
use ObsPager\Service\ObsidianService;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

/**
 * GeneratePagesCommand
 *
 * @author Florian Weber <git@fweber.info>
 */
class GeneratePagesCommand extends Command
{
    protected static $defaultName = 'generate-pages';

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @var GeneratorConfig
     */
    private GeneratorConfig $config;

    public function __construct(Environment $twig, GeneratorConfig $config)
    {
        parent::__construct();

        $this->twig = $twig;
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate the HTML Output for a Obsidian Folder')
            ->addArgument('obsidian-dir', InputArgument::REQUIRED, 'The source folder')
            ->addArgument('output-dir', InputArgument::REQUIRED, 'The output folder')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ObsidanException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sourceDir = $input->getArgument('obsidian-dir');
        $targetDir = rtrim(rtrim($input->getArgument('output-dir'), '/'), '\\');

        $io->title('Generating Pages');
        $io->info(sprintf('from %s to %s', $sourceDir, $targetDir));

        $io->section('gathering obsidian info');

        $obsInfo = ObsidianService::getInfo($sourceDir);

        $io->info(sprintf('workspace %s', $obsInfo->workspaceName));

        $io->section('generating...');

        $fileExts = ['md'];
        $iterator = new RecursiveDirectoryIterator($sourceDir);

        /** @var SplFileInfo $file */
        foreach(new RecursiveIteratorIterator($iterator) as $file) {
            if (!in_array($file->getExtension(), $fileExts)) {
                continue;
            }

            $io->text(sprintf('parsing %s', $file->getFilename()));

            $parsed = ObsidianService::parse($file);
            $dir = realpath($targetDir).'/'.ltrim(ltrim(str_replace(realpath($sourceDir), '', $file->getPath()), '/'), '\\');

            ObsidianService::dump($this->twig, $dir, $file, $parsed);
        }

        return Command::SUCCESS;
    }
}

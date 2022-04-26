<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Command;

use MykytaPopov\VPatch\Differ;
use MykytaPopov\VPatch\Finder;
use MykytaPopov\VPatch\PathResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{
    /**
     * @inerhitDoc
     */
    protected static $defaultName = 'generate';

    /**
     * @inerhitDoc
     */
    protected static $defaultDescription = 'Generate patch for vendor';

    /**
     * The extension of files with origin content
     *
     * @var string
     */
    private $extension = 'old';

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var PathResolver
     */
    private $pathResolver;

    public function __construct(string $name = null)
    {
        $this->finder = new Finder();
        $this->differ = new Differ();
        $this->pathResolver = new PathResolver();

        parent::__construct($name);
    }

    protected function configure()
    {
        $descPath = 'search path to the dir or direct path to file';
        $this->addOption('path', [], InputArgument::OPTIONAL, $descPath, getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = getcwd();
        if (!$this->pathResolver->checkCWD($cwd)) {
            $output->writeln(
                '<error>Can\'t resolve vendor and composer.json path, command should be executed from the project root dir</error>'
            );

            return Command::FAILURE;
        }

        $oldFiles = $this->finder->getOldFiles($input->getOption('path') . '/vendor', $this->extension);
        foreach ($oldFiles as $oldFilePath) {
            $output->writeln("<info>found old: {$oldFilePath}</info>");
            try {
                $patchPath = $this->generatePatch($cwd, (string)$oldFilePath);
                $output->writeln("<info>generated patch: {$patchPath}</info>");
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $colors = [
                    1 => 'error',
                    2 => 'comment'
                ];
                $color = $colors[$e->getCode()];
                $output->writeln("<{$color}>$message</{$color}>");
            }
            $output->writeln('==========');
        }

        return Command::SUCCESS;
    }

    /**
     * Generate patch from the old file
     *
     * @param string $cwd Current working dir to generate destination
     * @param string $oldFilePath Old file path to generate destination
     *
     * @return string Generated patch file path
     * @throws \Exception
     */
    private function generatePatch(string $cwd, string $oldFilePath): string
    {
        $originFilePath = substr($oldFilePath, 0, -4);

        $diff = $this->differ->compare($originFilePath, $oldFilePath);

        if (empty($diff)) {
            throw new \Exception('skip - no difference: ' . $originFilePath . ' -> ' . $oldFilePath, 2);
        }

        $patchPath = $this->pathResolver->getDestination($cwd, $oldFilePath);
        $result = file_put_contents($patchPath, $diff);

        if (!$result) {
            throw new \Exception('fail - file was not saved: ' . $patchPath, 1);
        }

        return $patchPath;
    }
}

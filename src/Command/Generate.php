<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Command;

use MykytaPopov\VPatch\Differ;
use MykytaPopov\VPatch\Finder;
use MykytaPopov\VPatch\PathResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Generate extends Command
{
    /** @inerhitDoc */
    protected static $defaultName = 'generate';

    private Finder $finder;

    private Differ $differ;

    private PathResolver $pathResolver;

    private Filesystem $fileSystem;

    private string $separator = '================================';

    /**
     * @inheritdoc
     */
    public function __construct(string $name = null)
    {
        $this->finder = new Finder();
        $this->differ = new Differ();
        $this->fileSystem = new Filesystem();
        $this->pathResolver = new PathResolver();

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Generate patch for vendor');

        $this->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Generate new diff even if it already exists (not safe, old data could be lost)'
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output, $input1): int
    {
        $cwd = getcwd();
        if (!$this->pathResolver->checkCWD($cwd)) {
            $output->writeln(
                "<error>can't find vendor and composer.json, run command from the project root</error>",
                OutputInterface::VERBOSITY_QUIET
            );

            return 1;
        }

        foreach ($this->finder->findFilesToCompare($cwd . '/vpatch') as $file) {
            $output->writeln(
                "<comment>Working on file:</comment> {$file->getPath()}",
                OutputInterface::VERBOSITY_DEBUG
            );

            $maskFilePath = 'vpatch/' . $file->getRelativePathname();
            $output->writeln("<info>mask:</info> {$maskFilePath}");

            $diffFilePath = $maskFilePath . '.diff';
            if (file_exists($diffFilePath) && !$input1->getOption('force')) {
                $output->writeln(["<comment>diff already exists:</comment> {$diffFilePath}", $this->separator]);

                continue;
            }

            $vendorFilePath = 'vendor/' . $file->getRelativePathname();
            if (!file_exists($vendorFilePath)) {
                $output->writeln(["<error>missing:</error> {$vendorFilePath}", $this->separator]);

                continue;
            }

            $diff = $this->differ->compare($maskFilePath, $vendorFilePath);
            $output->writeln('<comment>diff:</comment>' . PHP_EOL . $diff, OutputInterface::VERBOSITY_DEBUG);

            $this->fileSystem->dumpFile($diffFilePath, $diff);

            $output->writeln(["<info>diff:</info> {$diffFilePath}", $this->separator]);
        }

        $output->writeln('<info>done</info>');

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Command;

use MykytaPopov\VPatch\DifferInterface;
use MykytaPopov\VPatch\FinderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends AbstractCommand
{
    public const DIFF_EXT = 'diff';

    /** @inerhitDoc */
    protected static $defaultName = 'generate';

    private FinderInterface $finder;

    private DifferInterface $differ;

    private Filesystem $fileSystem;

    /** @var string Target dir with in origin files related to vendor */
    private string $targetDir = 'vpatch';

    /**
     * @inheritdoc
     */
    public function __construct(
        FinderInterface $finder,
        DifferInterface $differ,
        Filesystem $fileSystem,
        string $name = null
    ) {
        $this->finder = $finder;
        $this->differ = $differ;
        $this->fileSystem = $fileSystem;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->cwd = getcwd();

        $this->setDescription('Generate patch for vendor');

        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Generate new diff even if it already exists (not safe, old data could be lost)'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $omissions = $this->checkEnvironment();
        if ($omissions) {
            $format = '<error>Missing files or directories: %s. Run command from the project root.</error>';
            $output->writeln(sprintf($format, implode(', ', $omissions)));

            return self::FAILURE;
        }

        $files = $this->finder->findFilesToCompare($this->cwd . '/vpatch');

        foreach ($files as $file) {
            $output->writeln(
                "<comment>Working on file:</comment> {$file->getPath()}",
                OutputInterface::VERBOSITY_DEBUG
            );

            $maskFilePath = 'vpatch/' . $file->getRelativePathname();
            $output->writeln("<info>mask:</info> {$maskFilePath}");

            $diffFilePath = $maskFilePath . '.' . self::DIFF_EXT;
            if (file_exists($diffFilePath) && !$input->getOption('force')) {
                $output->writeln(["<comment>diff already exists:</comment> {$diffFilePath}", $this->separator]);

                continue;
            }

            $vendorFilePath = 'vendor/' . $file->getRelativePathname();
            if (!file_exists($vendorFilePath)) {
                $output->writeln(["<error>missing:</error> {$vendorFilePath}", $this->separator]);

                continue;
            }

            $diff = $this->differ->compare($vendorFilePath, $maskFilePath);
            $output->writeln('<comment>diff:</comment>' . PHP_EOL . $diff, OutputInterface::VERBOSITY_DEBUG);

            $this->fileSystem->dumpFile($diffFilePath, $diff);

            $output->writeln(["<info>diff:</info> {$diffFilePath}", $this->separator]);
        }

        $output->writeln('<info>done</info>');

        return self::SUCCESS;
    }

    /**
     * @return string[] Missed directories
     */
    private function checkEnvironment(): array
    {
        $omissions = [];

        $vendor = 'vendor';
        if (!file_exists($this->cwd . '/' . $vendor) || is_file($this->cwd . '/' . $vendor)) {
            $omissions[] = $vendor;
        }

        $composer = 'composer.json';
        if (!file_exists($this->cwd . '/' . $composer) || !is_file($this->cwd . '/' . $composer)) {
            $omissions[] = $composer;
        }

        if (!file_exists($this->cwd . '/' . $this->targetDir) || is_file($this->cwd . '/' . $this->targetDir)) {
            $omissions[] = $this->targetDir;
        }

        return $omissions;
    }
}

<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Command;

use MykytaPopov\VPatch\DifferInterface;
use MykytaPopov\VPatch\FinderInterface;
use MykytaPopov\VPatch\PathResolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends Command
{
    /** @inerhitDoc */
    protected static $defaultName = 'generate';

    private FinderInterface $finder;

    private DifferInterface $differ;

    private PathResolverInterface $pathResolver;

    private Filesystem $fileSystem;

    private string $separator = '================================';

    /**
     * @inheritdoc
     */
    public function __construct(
        FinderInterface $finder,
        DifferInterface $differ,
        PathResolverInterface $pathResolver,
        Filesystem $fileSystem,
        string $name = null
    ) {
        $this->finder = $finder;
        $this->differ = $differ;
        $this->fileSystem = $fileSystem;
        $this->pathResolver = $pathResolver;

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
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = getcwd();
        if (!$this->pathResolver->checkCWD($cwd)) {
            $output->writeln(
                "<error>can't find vendor and composer.json, run command from the project root</error>",
                OutputInterface::VERBOSITY_QUIET
            );

            return self::FAILURE;
        }

        foreach ($this->finder->findFilesToCompare($cwd . '/vpatch') as $file) {
            $output->writeln(
                "<comment>Working on file:</comment> {$file->getPath()}",
                OutputInterface::VERBOSITY_DEBUG
            );

            $maskFilePath = 'vpatch/' . $file->getRelativePathname();
            $output->writeln("<info>mask:</info> {$maskFilePath}");

            $diffFilePath = $maskFilePath . '.diff';
            if (file_exists($diffFilePath) && !$input->getOption('force')) {
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

        return self::SUCCESS;
    }
}

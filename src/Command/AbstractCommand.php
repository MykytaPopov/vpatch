<?php

declare(strict_types=1);

namespace MykytaPopov\VPatch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    protected string $separator = '================================';

    /** @var string Current Working Dir */
    protected string $cwd = '';

    /**
     * @inheritdoc
     */
    public function __construct(string $name = null)
    {
        $this->cwd = getcwd();

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('VPatch ' . exec('git describe --tags --abbrev=0') . ' <bg=yellow;options=bold>#StandWith</><bg=blue;options=bold>Ukraine</>', OutputInterface::VERBOSITY_QUIET);

        return self::SUCCESS;
    }
}

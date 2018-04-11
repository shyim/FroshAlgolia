<?php

namespace FroshAlgolia\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleProgressHelper implements ProgressHelperInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * @var int
     */
    private $count;

    /**
     * @var
     */
    private $current;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function start($count, $label = '')
    {
        $this->count = $count;
        $this->current = 0;
        if ($label) {
            $this->output->writeln($label);
        }
        $this->progress = new ProgressBar($this->output, $count);
        $this->progress->setFormat('very_verbose');
    }

    /**
     * {@inheritdoc}
     */
    public function advance($step = 1)
    {
        if ($this->current + $step > $this->count) {
            $step = $this->count - $this->current;
        }
        $this->progress->advance($step);
        $this->current += $step;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        $this->progress->finish();
        $this->output->writeln('');
    }
}

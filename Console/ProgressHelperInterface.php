<?php

namespace FroshAlgolia\Console;

/**
 * Interface ProgressHelperInterface.
 */
interface ProgressHelperInterface
{
    /**
     * Initials the progress with the provided count.
     * Allows to provide a label to display a message before the progress starts.
     *
     * @param int    $count
     * @param string $label
     */
    public function start($count, $label = '');

    /**
     * Advance the progress with the provided value.
     *
     * @param int $step
     */
    public function advance($step = 1);

    /**
     * Finish the progress bar.
     */
    public function finish();
}

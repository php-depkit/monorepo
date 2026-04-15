<?php

declare(strict_types=1);

namespace PhpDepkit\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use RuntimeException;
use SebastianFeldmann\Git\Repository;

final class RunComposerScript implements Action
{
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $script = $this->script($action);
        $label = $action->getLabel();

        $process = proc_open(
            command: ['composer', 'run-script', $script, '--no-interaction'],
            descriptor_spec: [
                0 => ['pipe', 'r'],
                1 => STDOUT,
                2 => STDERR,
            ],
            pipes: $pipes,
            cwd: dirname(__DIR__, 2),
        );

        if (! is_resource($process)) {
            throw new RuntimeException(sprintf('Could not start %s.', $label));
        }

        fclose($pipes[0]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException(sprintf('%s failed with exit code %d.', $label, $exitCode));
        }
    }

    private function script(Config\Action $action): string
    {
        $script = $action->getOptions()->get('script');

        if (! is_string($script) || $script === '') {
            throw new RuntimeException('CaptainHook action is missing the required "script" option.');
        }

        return $script;
    }
}

<?php

declare(strict_types=1);

namespace PhpDepkit\MonorepoBuilder\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;

final class AfterReleaseSetNextMutualDependenciesReleaseWorker implements StageAwareInterface
{
    public function __construct(
        private readonly SetNextMutualDependenciesReleaseWorker $releaseWorker,
    ) {
    }

    public function getDescription(Version $version): string
    {
        return $this->releaseWorker->getDescription($version);
    }

    public function work(Version $version): void
    {
        $this->releaseWorker->work($version);
    }

    public function getStage(): string
    {
        return 'after-release';
    }
}

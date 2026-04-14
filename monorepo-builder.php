<?php

declare(strict_types=1);

use PhpDepkit\MonorepoBuilder\ReleaseWorker\AfterReleasePushNextDevReleaseWorker;
use PhpDepkit\MonorepoBuilder\ReleaseWorker\AfterReleaseSetNextMutualDependenciesReleaseWorker;
use PhpDepkit\MonorepoBuilder\ReleaseWorker\AfterReleaseUpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/packages']);
    $mbConfig->defaultBranch('main');

    MBConfig::disableDefaultWorkers();

    $parameters = $mbConfig->parameters();
    $parameters->set(Option::IS_STAGE_REQUIRED, true);
    $parameters->set(Option::STAGES_TO_ALLOW_EXISTING_TAG, ['after-release']);

    $services = $mbConfig->services();
    $services->set(SetNextMutualDependenciesReleaseWorker::class);
    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);

    $mbConfig->workers([
        AfterReleaseSetNextMutualDependenciesReleaseWorker::class,
        AfterReleaseUpdateBranchAliasReleaseWorker::class,
        AfterReleasePushNextDevReleaseWorker::class,
    ]);
};

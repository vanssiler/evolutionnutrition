<?php

namespace Mundipagg\Core\Hub\Commands;

use Exception;
use Mundipagg\Core\Kernel\Abstractions\AbstractModuleCoreSetup as  MPSetup;
use Mundipagg\Core\Kernel\Aggregates\Configuration;
use Mundipagg\Core\Kernel\Factories\ConfigurationFactory;
use Mundipagg\Core\Kernel\Repositories\ConfigurationRepository;

class UninstallCommand extends AbstractCommand
{
    public function execute()
    {
        $moduleConfig = MPSetup::getModuleConfiguration();

        if (!$moduleConfig->isHubEnabled()) {
            throw new Exception("Hub is not installed!");
        }

        $hubKey = $moduleConfig->getSecretKey();
        if (!$hubKey->equals($this->getAccessToken())) {
            throw new Exception("Access Denied.");
        }

        $cleanConfig = json_decode(json_encode($moduleConfig));
        $cleanConfig->keys = [
            Configuration::KEY_SECRET => null,
            Configuration::KEY_PUBLIC => null,
        ];
        $cleanConfig->testMode = true;
        $cleanConfig->hubInstallId = null;

        $cleanConfig = json_encode($cleanConfig);
        $configFactory = new ConfigurationFactory();
        $cleanConfig = $configFactory->createFromJsonData($cleanConfig);
        $cleanConfig->setId($moduleConfig->getId());
        MPSetup::setModuleConfiguration($cleanConfig);
        
        $configRepo = new ConfigurationRepository();

        $configRepo->save($cleanConfig);
    }
}
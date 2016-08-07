<?php
namespace Webfox\T3events\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Webfox\T3events\Domain\Model\Dto\ModuleData;
use Webfox\T3events\Service\ModuleDataStorageService;

/**
 * Class ModuleDataTrait
 * Provides functionality for backend module controller
 *
 * @package Webfox\T3events\Controller
 */
trait ModuleDataTrait
{
    /**
     * @var \Webfox\T3events\Domain\Model\Dto\ModuleData
     */
    protected $moduleData;

    /**
     * @var \Webfox\T3events\Service\ModuleDataStorageService
     */
    protected $moduleDataStorageService;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @return array
     */
    abstract public function mergeSettings();

    /**
     * Forwards the request to another action and / or controller.
     * Request is directly transfered to the other action / controller
     * without the need for a new request.
     *
     * @param string $actionName Name of the action to forward to
     * @param string $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
     * @param string $extensionName Name of the extension containing the controller to forward to. If not specified, the current extension is assumed.
     * @param array $arguments Arguments to pass to the target action
     * @return void
     */
    abstract public function forward(
        $actionName,
        $controllerName = null,
        $extensionName = null,
        array $arguments = null
    );

    /**
     * injects the module data storage service
     *
     * @param ModuleDataStorageService $service
     */
    public function injectModuleDataStorageService(ModuleDataStorageService $service)
    {
        $this->moduleDataStorageService = $service;
    }

    /**
     * Gets the module key
     *
     * @return string
     */
    protected function getModuleKey()
    {
        return $GLOBALS['moduleName'];
    }

    /**
     * initializes all action methods
     */
    public function initializeAction()
    {
        $this->pageUid = (int)GeneralUtility::_GET('id');
        $this->settings = $this->mergeSettings();
    }

    /**
     * Reset action
     * Resets all module data and forwards the request to the list action
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function resetAction()
    {
        $this->moduleData = $this->objectManager->get(ModuleData::class);
        $this->moduleDataStorageService->persistModuleData($this->moduleData, $this->getModuleKey());
        $this->forward('list');
    }
}

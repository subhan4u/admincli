<?php


namespace SubhanSayyed\Cli\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Console\CommandListInterface;
use SubhanSayyed\Cli\Model\Config;
use Magento\Framework\AuthorizationInterface;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CommandListInterface
     */
    private $commandList;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * Form constructor.
     * @param Template\Context $context
     * @param CommandListInterface $commandList
     * @param Config $config
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CommandListInterface $commandList,
        Config $config,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        $this->commandList = $commandList;
        $this->config = $config;
        $this->authorization = $authorization;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Command Line by SubhanSayyed'));
    }

    /**
     * Retrieve true if exec funtion is accesible
     * @return bool
     */
    public function execExist()
    {
        return function_exists('exec');
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->config->isEnabled();
    }
}

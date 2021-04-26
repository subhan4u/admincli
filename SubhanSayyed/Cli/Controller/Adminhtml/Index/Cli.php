<?php

namespace SubhanSayyed\Cli\Controller\Adminhtml\Index;

use Magento\Framework\Exception\LocalizedException;
use SubhanSayyed\Cli\Model\Config;

class Cli extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'SubhanSayyed_Cli::elements';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * Backend auth session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Backend\Model\Auth\Session $authSession,
        Config $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->dir = $dir;
        $this->authSession = $authSession;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface|null
     */
    public function execute()
    {
        try {
            if (!$this->config->isEnabled()) {
                throw new LocalizedException(
                    __('Command Line Interface is disabled, to enable the extension please navigate to Stores
> Configuration > SubhanSayyed > Command Line Interface.'),
                    1
                );
            }

            $this->validateUser();

            $command = $this->getRequest()->getParam('command');

            if (!$this->_authorization->isAllowed('SubhanSayyed_Cli::admin')) {
                $needle = 'bin/magento ';
                $position = stripos($command, $needle);
                $needleLen = strlen($needle);
                $commandStart = $position + $needleLen;
                $magentoCommand = substr($command, $commandStart);

                if ($position === false) {
                    throw new LocalizedException(__('You don\'t have permission to execute this command.'), 1);
                }
            }

            if (strpos($command, 'cd') === 0) {
                throw new LocalizedException(__('cd command is not supported.'), 1);
            }

            $logFile = $this->dir->getPath('var') . '/subline.txt';
            // @codingStandardsIgnoreStart
            exec($c = 'cd ' . $this->dir->getRoot() . ' && ' . $command . ' > ' . $logFile, $a, $b);
            $message = file_get_contents($logFile);
            if (!$message) {
                $message = __('Command not found or error occurred.') . PHP_EOL;
            }
            unlink($logFile);
            // @codingStandardsIgnoreEnd
        } catch (LocalizedException $e) {
            $message = $e->getMessage() . PHP_EOL;
        }

        $response = ['message' => nl2br($message)];

        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * Validate current user password
     *
     * @return $this
     * @throws UserLockedException
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Exception
     */
    protected function validateUser()
    {
        $password = $this->getRequest()->getParam(
            \Magento\User\Block\Role\Tab\Info::IDENTITY_VERIFICATION_PASSWORD_FIELD
        );
        if (!$password) {
            throw new LocalizedException(__('Please enter your password.'));
        }
        $user = $this->authSession->getUser();
        $user->performIdentityCheck($password);

        return $this;
    }
}

<?php


declare(strict_types=1);

namespace SubhanSayyed\Cli\Block\Adminhtml\System\Config\Form;

class Info extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Return extension title
     * @return string
     */
    protected function getModuleTitle():string
    {
        return 'Command Line Interface';
    }
}

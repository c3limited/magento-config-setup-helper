<?php
/**
 * Abstract config form element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class C3_ConfigSetupHelper_Block_System_Config_Form_Field
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Decorate field row html
     * @modification Add checkbox to mark which fields we want to output
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        // If enabled, add checkboxes to this field
        if (Mage::getStoreConfig('c3_configsetuphelper/options/enabled')) {
            // Change name so that it is different to the actual values being saved
            $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
            $namePrefix = preg_replace('#^groups#', 'show_config', $namePrefix);

            $extraInput = "<input type=\"checkbox\" name=\"{$namePrefix}\" value=\"1\" style=\"float:left;margin-right:6px\" />";
            $html = preg_replace('/^(<td[^>]*>)/', "$1{$extraInput}", $html);
        }

        return parent::_decorateRowHtml($element, $html);
    }
}

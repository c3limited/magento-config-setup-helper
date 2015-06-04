<?php
/**
 * C3 Media Ltd
 *
 * @title       Config Setup Helper
 * @category    C3
 * @package     C3_ConfigSetupHelper
 * @author      C3 Development Team <development@c3media.co.uk>
 * @copyright   Copyright (c) 2014 C3 Media Ltd (http://www.c3media.co.uk)
 */

/**
 * Abstract config form element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            $namePrefix = preg_replace(
                '#\[value\](\[\])?$#', '', $element->getName()
            );
            $namePrefix = preg_replace('#^groups#', 'show_config', $namePrefix);

            $extraInput
                = "<input type=\"checkbox\" name=\"{$namePrefix}\" value=\"1\" style=\"float:left;margin-right:6px\" />";
            $html = preg_replace(
                '/^((?:<tr[^>]*>)?\s*<td[^>]*>)/', "$1{$extraInput}", $html
            );
        }

        if (substr($html, 3) == '<tr') {
            return $html;
        }
        return parent::_decorateRowHtml($element, $html);
    }


    //@modification: overriding render function in order to make compatible with scopehint
    public function render(Varien_Data_Form_Element_Abstract $element)
    {

        if (Mage::helper('core')->isModuleEnabled('AvS_ScopeHint')) {
            //get block from AvS_ScopeHint and render

            $html = Mage::app()->getLayout()->createBlock(
                'scopehint/AdminhtmlSystemConfigFormField'
            )->render($element);

            $html = $this->_decorateRowHtml($element, $html);


        } else {
            $html = parent::render($element);
        }


        return $html;
    }

}

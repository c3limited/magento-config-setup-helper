<?php

/**
 * Observer for config setup helper
 */
class C3_ConfigSetupHelper_Model_Observer
{
    /**
     * Display config entries (as passed by form) in notification area ready to add to setup script
     *
     * @param $ob
     */
    public function showSelectedConfig($ob)
    {
        // If not enabled, or if no checkboxes ticked, skip
        if (!Mage::getStoreConfig('c3_configsetuphelper/options/enabled') || !isset($_REQUEST['show_config'])) {
            return;
        }

        // Helper for translations
        $helper = Mage::helper('c3_configsetuphelper');

        $website = $ob->getWebsite();
        $store = $ob->getStore();
        $section = $ob->getSection();

        // Get fully qualified field names from form
        $paths = array();
        foreach (Mage::app()->getRequest()->getParam('show_config') as $groupname => $group) {
            foreach (array_keys($group['fields']) as $fieldname) {
                $fullName = "{$section}/{$groupname}/{$fieldname}";
                $paths[] = $fullName;
            }
        }

        // Get path values with correct scope
        $pathValues = $this->_getPathValues($paths, $website, $store);

        // Output values in string per group
        $string = '<pre>' . $this->_getHeader() . '// ' . $helper->__('Config for') . ' ' . $helper->__($section) . "\n";
        foreach (Mage::app()->getRequest()->getParam('show_config') as $groupname => $group) {
            $string .= "\n// " . $helper->__('Settings for %s group', "'".$groupname."'"). "\n";
            foreach (array_keys($group['fields']) as $fieldname) {
                $fullName = "{$section}/{$groupname}/{$fieldname}";
                // Skip if name was not retrieved
                if (!isset($pathValues[$fullName])) {
                    continue;
                }
                $showVal = "'" . addslashes($pathValues[$fullName]) . "'";
                if ($pathValues[$fullName] === null) {
                    $showVal = 'null';
                }
                $string .= "\$installer->setConfigData('" . addslashes($fullName) . "', $showVal);\n";
            }
        }
        $string .=  $this->_getFooter() . '</pre>';

        // Output as notice to session
        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__($string));
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return '/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

';
    }

    /**
     * @return string
     */
    protected function _getFooter()
    {
        return '$installer->endSetup();';
    }

    /**
     * Get config values for given paths from database
     *
     * @param array $paths
     * @param null|string $website
     * @param null|string $store
     * @return object
     */
    protected function _getPathValues($paths, $website=null, $store=null)
    {
        // Get collection with correct scope and paths
        $collection = Mage::getModel('core/config_data')->getCollection();
        if ($store !== null) {
            $collection->addFieldToFilter('scope','store');
            $collection->addFieldToFilter('scope_id',$store);
        } elseif ($website !== null) {
            $collection->addFieldToFilter('scope','website');
            $collection->addFieldToFilter('scope_id',$website);
        } else {
            $collection->addFieldToFilter('scope','default');
        }
        $collection->addFieldToFilter('path', array('in' => $paths));

        // Get value per path
        $pathValues = array();
        foreach ($collection as $configrow) {
            $pathValues[$configrow['path']] = $configrow['value'];
        }

        return $pathValues;
    }
}

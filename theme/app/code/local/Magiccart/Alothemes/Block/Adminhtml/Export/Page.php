<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-07-25 14:51:41
 * @@Modify Date: 2016-06-07 23:28:16
 * @@Function:
 */

?>
<?php
class Magiccart_Alothemes_Block_Adminhtml_Export_Page extends Mage_Adminhtml_Block_Cms_Page_Grid
{

    protected function _prepareMassaction()
    {
	    $this->setMassactionIdField('page_id');
	    $this->getMassactionBlock()->setFormFieldName('page_ids');

        $stores = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false);
        $this->getMassactionBlock()->addItem('export', array(
            'label'    => Mage::helper('adminhtml')->__('Export'),
            'url'      => $this->getUrl('*/*/exportXml'),
            'additional' => array(
            'visibility' => array(
                    'name' => 'store_id',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('adminhtml')->__('Store'),
                    'values' => $stores
                )
            ),
            'confirm'  => Mage::helper('adminhtml')->__('Are you sure?')
        ));
	    return $this;
    }

}


<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-08-07 22:10:30
 * @@Modify Date: 2015-10-01 12:10:45
 * @@Function:
 */

class Magiccart_Shopbrand_Model_System_Config_Column
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1,   'label'=>Mage::helper('adminhtml')->__('1 item')),
            array('value' => 2,   'label'=>Mage::helper('adminhtml')->__('2 items')),
            array('value' => 3,   'label'=>Mage::helper('adminhtml')->__('3 items')),
            array('value' => 4,   'label'=>Mage::helper('adminhtml')->__('4 items')),
            array('value' => 5,   'label'=>Mage::helper('adminhtml')->__('5 items')),
            array('value' => 6,   'label'=>Mage::helper('adminhtml')->__('6 items')),
            array('value' => 7,   'label'=>Mage::helper('adminhtml')->__('7 items')),
            array('value' => 8,   'label'=>Mage::helper('adminhtml')->__('8 items')),
            array('value' => 9,   'label'=>Mage::helper('adminhtml')->__('9 items')),
            array('value' => 10,  'label'=>Mage::helper('adminhtml')->__('10 items')),
        );
    }
}

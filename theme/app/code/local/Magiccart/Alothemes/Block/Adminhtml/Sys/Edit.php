<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-07-25 14:47:03
 * @@Modify Date: 2016-06-16 10:56:01
 * @@Function:
 */
?>
<?php
class Magiccart_Alothemes_Block_Adminhtml_Sys_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back');
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'alothemes';
        $this->_controller = 'adminhtml_sys';
        
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Export'));
        $this->_removeButton('reset');
		
    
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('system_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'system_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'system_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('system_data') && Mage::registry('system_data')->getId() ) {
            return Mage::helper('alothemes')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('system_data')->getTitle()));
        } else {
            return Mage::helper('alothemes')->__('');
        }
    }
}


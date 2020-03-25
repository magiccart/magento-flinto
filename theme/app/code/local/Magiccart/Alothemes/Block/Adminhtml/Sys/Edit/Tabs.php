<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-07-25 14:47:03
 * @@Modify Date: 2016-06-16 10:56:21
 * @@Function:
 */
?>
<?php
class Magiccart_Alothemes_Block_Adminhtml_Sys_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('exportconfig_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('adminhtml')->__('Item Information'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('adminhtml')->__('Export system config'),
			'title'     => Mage::helper('adminhtml')->__('Export system config'),
			'content'   => $this->getLayout()->createBlock('alothemes/adminhtml_sys_edit_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}


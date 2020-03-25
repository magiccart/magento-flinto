<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-07-25 15:01:10
 * @@Modify Date: 2016-06-07 23:34:40
 * @@Function:
 */

?>
<?php
class Magiccart_Alothemes_Adminhtml_Export_TestiController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected $dir = 'import';
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('magiccart/alothemes')
            ->_title(Mage::helper('alothemes')->__('Manage Export Testimonial'))
            ->_addBreadcrumb(Mage::helper('alothemes')->__('Manage Export Testimonial'), Mage::helper('alothemes')->__('Manage Export Magicslider'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('alothemes/adminhtml_export_testi'));
        $this->renderLayout();
    }

    /**
     *  Export page grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'testimonial.xml';
        $slideIds = $this->getRequest()->getParam('testimonial_ids');
        $storeId = (int) $this->getRequest()->getParam('store_id', 0);
        if(!is_array($slideIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('testimonial/testimonial')->getCollection()->addFieldToFilter('testimonial_id',array('in'=>$slideIds));
                $options = array('name', 'text', 'image', 'company', 'email', 'website', 'rating_summary', 'position', 'status');
                $xml = '<root>';
                    $xml.= '<testimonial>';
                    $num = 0;
                    foreach ($collection as $slide) {
                        $xml.= '<item>';
                        foreach ($options as $opt) {
                            $xml.= '<'.$opt.'><![CDATA['.$slide->getData($opt).']]></'.$opt.'>';
                        }
                        $xml.= '</item>';
                        $num++;
                    }
                    $xml.= '</testimonial>';
                $xml.= '</root>';
               // $this->_sendUploadResponse($fileName, $xml);
                $theme = Mage::getStoreConfig('design/theme/default', $storeId);
                if(!$theme) $theme = 'default';
                $moduleName = 'Magiccart_Alothemes'; //'Magiccart_' . ucfirst(Mage::app()->getRequest()->getModuleName());
                $folder = Mage::getModuleDir('etc', $moduleName) .DIRECTORY_SEPARATOR. $this->dir .DIRECTORY_SEPARATOR. $theme;
                @mkdir($folder, 0644, true);
                $doc =  new DOMDocument('1.0', 'UTF-8');
                $doc->loadXML($xml);
                $doc->formatOutput = true;
                $doc->save("$folder/$fileName");
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Export (%s) Item Slider:', $num)
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function _isAllowed() {     return true; }

}


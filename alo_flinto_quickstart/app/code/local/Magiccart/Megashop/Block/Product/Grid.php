<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-03-14 20:26:27
 * @@Modify Date: 2016-09-05 21:55:27
 * @@Function:
 */
?>
<?php
class Magiccart_Megashop_Block_Product_Grid extends Mage_Catalog_Block_Product_List
{

    // protected $_productCollection;
    protected $_limit;
    protected $_type;

    public function getType()
    {
        $type = $this->getRequest()->getParam('type');
        if(!$type){
            $type = $this->getActive(); // get form setData in Block
        }
        return $type;
    }

    public function getWidgetCfg($cfg=null)
    {
        $info = $this->getRequest()->getParam('info');
        if($info){
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;          
        }else {
            $info = $this->getCfg();
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;
        }
    }

    public function isRootCategoryFilterMode()
    {
        if(!$this->isSingleCateogryMode()) return Mage::app()->getStore()->getRootCategoryId();
    }

    public function isSingleCateogryMode(){
        $groups = Mage::app()->getGroups();
        if(count($groups) ==1) return true;
        $CatIds = array();
        foreach ($groups as $group) {
            $CatIds[] = $group->getRootCategoryId();
        }
        $average = array_sum($CatIds) / count($CatIds);
        if($average == $CatIds[0]) return true;
        return false;
    }

    public function categoryFilter($collection)
    {
        $cfg = true; // get from config or Widget
        if($cfg){
            $catId = (int) $this->getWidgetCfg('category_id'); //(int) $this->getRequest()->getPost('category_id');
            // if(!$catId) {$catId = Mage::registry('current_category');}
            if($catId){
                $Category = Mage::getModel('catalog/category')->load($catId);
                Mage::register('current_category', $Category, true);
                $collection->addCategoryFilter($Category); // not use RootCatId
            }else {
                $catId = $this->isRootCategoryFilterMode();
                if($catId){
                    // $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                    //            ->addAttributeToFilter('category_id', array('in' => $catId));
                    $category = Mage::getModel('catalog/category')->load($catId);
                    //$catIds = explode(',',$category->getChildren());
                    $catIds = explode(',',$category->getAllChildren());
                    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                               ->addAttributeToFilter('category_id', array('in' => $catIds))
                               ->groupByAttribute('entity_id');    //->getSelect()->group('e.entity_id');        
                }
            }
        }
        return $collection;
    }

    protected function _getProductCollection()
    {
        
        $this->_limit   = $this->getWidgetCfg('limit');
        $this->_type    = $this->getType(); // $this->getActivated();
        $isCategory     = is_numeric($this->_type);
        $categoryId = $isCategory ? $this->_type : $this->getWidgetCfg('category_id');
        if (is_null($this->_productCollection)) {
            $this->setCategoryId($categoryId); //$this->setCategoryId($this->getType());
            $this->_productCollection = parent::_getProductCollection();   
        }

        // $this->_types = $this->getWidgetCfg('types');
        if($isCategory || !$this->_type) return $this->_productCollection->setPageSize($this->_limit);
        $fn = 'get' . ucfirst( $this->_type);
        $collection = $this->{$fn}($this->_productCollection);
        return $collection->setPageSize($this->_limit);

    }


    public function getBestseller($collection){

        // set Store
        $storeIds = Mage::app()->getGroup()->getStoreIds(); // filter follow store;
        //$storeIds  = $this->getStoreId(); // filter follow store view
        $storeId = implode(',', $storeIds);
        $ids = implode(',', $collection->getAllIds());
        if(!$ids) return $collection;
        // set Limit
        $limit    = $this->_limit;
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = Mage::getConfig()->getTablePrefix();

        $sql = "SELECT max(qo) AS des_qty,`product_id`,`parent_item_id`
            FROM ( SELECT sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id,`parent_item_id` FROM {$tablePrefix}sales_flat_order_item GROUP BY `product_id` )
            AS t1 where store_id IN ({$storeId})
            AND product_id IN ({$ids})
            AND parent_item_id is null
            GROUP BY `product_id` ORDER BY des_qty DESC LIMIT {$limit}";

        $rows = $read->fetchAll($sql);
        $producIds = array();
        foreach ($rows as $row) { $producIds[] = $row['product_id'];}
        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
        return $collection;
    }

    public function getFeatured($collection){

        $collection->addAttributeToFilter('featured', '1');

        return $collection; 
    }

    public function getLatest($collection){

        $collection = $collection->addStoreFilter()
        ->addAttributeToSort('entity_id', 'desc');

        return $collection;

    }

    public function getMostviewed($collection){
        //Magento get popular products by total number of views
        $ids = $collection->getAllIds();
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $report = Mage::getResourceModel('reports/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToFilter('entity_id', array('in' => $ids))
                            ->addViewsCount()
                            ->setPageSize($this->_limit)
                            ->setCurPage(1);

        $producIds = $report->getAllIds();
        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
    
        return $collection;

    }

    public function getNewproduct($collection) {

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = $collection->addStoreFilter()
                            ->addAttributeToFilter('news_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('news_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('news_from_date', 'desc');

        return $collection;
    }

    public function getRandom($collection) {

        $collection->getSelect()->order('rand()');
        return $collection;
    }

    public function getReview($collection) {

    }

    public function getSaleproduct($collection){

        $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = $collection->addAttributeToFilter('special_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('special_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('special_to_date','desc')
                            ->addTaxPercents()
                            ->addStoreFilter();

        return $collection;
    }


}

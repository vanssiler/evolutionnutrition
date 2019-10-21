<?php
class Vsourz_Bannerslider_Block_Adminhtml_Slider extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_category';
		$this->_blockGroup = 'bannerslider'; 
		/* please not this is the block group the grid is called in this fashion: ($this->_blockGroup._.$this->_controller._.grid) */
		$this->_headerText = Mage::helper('bannerslider')->__('Category Slider Manager');
		$this->_addButtonLabel = Mage::helper('bannerslider')->__('Add Category Slider');
		parent::__construct();
	}
}

<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Module\Portfolio\Site\Dispatcher;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Input\Input;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_tz_portfolio
 *
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   4.2.0
     */
    protected function getLayoutData()
    {

        $data   = parent::getLayoutData();
        $helper = $this -> getHelperFactory()-> getHelper('PortfolioHelper', $data);

        /**
         * Load styles and scripts of com_tz_portfolio to use this module
         * @var WebAssetManager $wa
         * */
        $wa     = $this->getApplication() -> getDocument() -> getWebAssetManager();

        $module = $data['module'];

        $wa -> getRegistry() -> addExtensionRegistryFile('com_tz_portfolio');
        $wa -> registerScript($module -> module.'.resize', Uri::base().'/media/'.$module -> module);
//        $wa -> usePreset('com_tz_portfolio.uikit');

        $data['list'] = $helper -> getList($data['params'], $this->getApplication());

        $data['tags']       = $helper -> getTagsByArticle($data['params'], $this->getApplication());
        $data['categories'] = $helper -> getCategoriesByArticle($data['params'], $this->getApplication());

        $show_filter = $data['params'] -> get('show_filter',1);

        if($show_filter) {
            $data['filter_tag'] = $helper -> getTagsFilterByArticle($data['params'], $this->getApplication());
            $data['filter_cat'] = $helper -> getCategoriesFilterByArticle($data['params'], $this->getApplication());
        }

        return $data;
    }

//    public function dispatch()
//    {
////        $wa     = Factory::getDocument() -> getWebAssetManager();
////        $wa -> usePreset('com_tz_portfolio.uikit');
//
//        parent::dispatch();
//    }
}

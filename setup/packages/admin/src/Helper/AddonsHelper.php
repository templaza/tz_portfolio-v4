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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class AddonsHelper{
    protected static $cache = array();

    public static function folderOptions()
    {
        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('DISTINCT(folder) AS value, folder AS text')
            ->from('#__tz_portfolio_plus_extensions')
            ->where('(type =' . $db->quote('tz_portfolio-addon').' OR type =  '.
                $db -> quote('tz_portfolio_plus-plugin').')')
            ->order('folder');

        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
            self::$cache[$storeId]  = $options;
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication() -> enqueueMessage($e->getMessage(), 'error');
        }

        return $options;
    }

    public static function getAddons($options = array()){
        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($options);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = Factory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('(type =' . $db->quote('tz_portfolio-addon').' OR type =  '.
            $db -> quote('tz_portfolio_plus-plugin').')');

        if(count($options)){
            if(isset($options['published'])){
                if(is_array($options['published'])) {
                    $query->where('published IN('.implode($options['published']).')');
                }else{
                    $query -> where('published='.$options['published']);
                }
            }else{
                $query -> where('(published = 0 OR published = 1)');
            }
            if(isset($options['protected'])){
                if(is_array($options['protected'])) {
                    $query->where('protected IN('.implode($options['protected']).')');
                }else{
                    $query -> where('protected='.$options['protected']);
                }
            }
            if(isset($options['folder']) && $options['folder']){
                $query -> where('folder='.$db -> quote($options['folder']));
            }
        }
        $db -> setQuery($query);
        if($data = $db -> loadObjectList()){
            self::$cache[$storeId]  = $data;
            return $data;
        }
        return false;
    }

    public static function getTotal($options = array()){
        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($options);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = Factory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('COUNT(e.id)');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('(type =' . $db->quote('tz_portfolio-addon').' OR type =  '.
            $db -> quote('tz_portfolio_plus-plugin').')');

        if(count($options)){
            if(isset($options['published'])){
                if(is_array($options['published'])) {
                    $query->where('e.published IN('.implode($options['published']).')');
                }else{
                    $query -> where('e.published='.$options['published']);
                }
            }else{
                $query -> where('(e.published = 0 OR e.published = 1)');
            }
            if(isset($options['protected'])){
                if(is_array($options['protected'])) {
                    $query->where('e.protected IN('.implode($options['protected']).')');
                }else{
                    $query -> where('e.protected='.$options['protected']);
                }
            }
            if(isset($options['folder']) && $options['folder']){
                $query -> where('e.folder='.$db -> quote($options['folder']));
            }
        }
        $db -> setQuery($query);
        if($data = $db -> loadResult()){
            self::$cache[$storeId]  = $data;
            return $data;
        }

        return 0;
    }
}
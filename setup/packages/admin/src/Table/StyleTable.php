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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Table;

//no direct access
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die('Restricted access');

class StyleTable extends Table
{
    public function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_templates','id',$db);
    }

    public function hasData(){
        $query = $this->_db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->_tbl);
        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        if($count > 0){
            return true;
        }
        return false;
    }

    public function hasHome(){
        return $this -> _hasHome();
    }

    protected function _hasHome(){
        $query  = $this -> _db -> getQuery(true)
            -> select('COUNT(*)')
            -> from($this -> _tbl)
            -> where($this -> _db -> quoteName('home').'= 1');
        $this -> _db -> setQuery($query);
        $count  = $this -> _db -> loadResult();
        if($count){
            return true;
        }
        return false;
    }
    public function getHome(){
        try{
            $query  = $this -> _db -> getQuery(true)
                -> select('*')
                -> from($this -> _tbl)
                -> where($this -> _db -> quoteName('home').'= 1');
            $this -> _db -> setQuery($query);
            $data = $this -> _db -> loadObject();

            if($data){
                foreach($data as $key => $val){
                    $this -> set($key,$val);
                }
            }
            return $data;
        }catch (\InvalidArgumentException $e)
        {
            $this->setError(Text::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $e -> getMessage()));
            return false;
        }
    }

    public function delete($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = is_null($pk) ? $this->$k : $pk;

        if (!is_null($pk))
        {
            $query = $this->_db->getQuery(true)
                ->from($this -> _tbl)
                ->select('id')
                ->where('template=' . $this->_db->quote($this->template));
            $this->_db->setQuery($query);
            $results = $this->_db->loadColumn();

            if (count($results) == 1 && $results[0] == $pk)
            {
                $this->setError(Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLE_ERROR_CANNOT_DELETE_LAST_STYLE'));

                return false;
            }
        }

        return parent::delete($pk);
    }
    public function store($updateNulls = false)
    {
        if(!$this ->protected){
            $this ->protected = 0;
        }
        if(!$this ->params){
            $this ->params = '';
        }
        return parent::store($updateNulls); // TODO: Change the autogenerated stub
    }
}
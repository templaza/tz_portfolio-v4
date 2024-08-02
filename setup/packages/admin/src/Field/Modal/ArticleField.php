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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field\Modal;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Form\Field\CheckboxesField;

// No direct access
defined('_JEXEC') or die;

class ArticleField extends CheckboxesField
{

    protected $type             = 'Modal_Article';
    protected $multiple         = false;
    protected $forceMultiple    = false;
    protected $layout           = 'form.field.modal.article';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);
        $this -> multiple   = false;
        if($element){

            if(isset($element['edit'])){
                $edit   = (string) $element['edit'];
                if($edit == 'true' || $edit == '1'){
                    $this -> element['edit']    = true;
                }else{
                    $this -> element['edit']    = false;
                }
            }
            if(isset($element['multiple'])){
                $multiple   = (string) $element['multiple'];
                if($multiple == 'true' || $multiple == '1' || $multiple == 'multiple'){
                    $this -> multiple    = true;
                    $this -> forceMultiple  = true;
                }
            }
        }
        if($this -> multiple){
            $this -> layout = 'form.field.modal.articles';
        }
        if(!$this -> multiple){
            $this -> value  = (int) $this -> value;
        }

        return $return;
    }
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $data['title']      = null;
        $data['items']      = false;
        $data['submitform'] = false;
        $data['link']       = 'index.php?option=com_tz_portfolio&view=articles&layout=modal'
            .($this -> multiple?'&ismultiple=true':'').'&tmpl=component';

        if($items  = $this -> _getItems($this -> value)){
            if($this -> multiple) {
                $data['items'] = $items;
            }else {
                $data['title'] = $items[0]->title;
            }
        }
        if (isset($this->element['language']))
        {
            $data['language']   = $this -> element['language'];
            $data['link']      .= '&forcedLanguage='.$this->element['language'];
        }

        if(isset($this -> element['submitform']) && $this -> element['submitform']){
            $data['submitform'] = boolval($this -> element['submitform']);
        }

        return $data;
    }

    protected function getRenderer($layoutId = 'default')
    {
        $renderer = new FileLayout($layoutId,COM_TZ_PORTFOLIO_ADMIN_PATH.'/layouts');

        $renderer->setDebug($this->isDebugEnabled());

        $layoutPaths = $this->getLayoutPaths();

        if ($layoutPaths)
        {
            $renderer->setIncludePaths($layoutPaths);
        }

        return $renderer;
    }

    protected function getLayoutPaths()
    {
        return array();
    }

    protected function _getItems($ids){
        if($ids){
            $db     = Factory::getDbo();
            $query  = $db -> getQuery(true);

            $query -> select('a.id,a.title,c.title AS category_title');
            $query -> from('#__tz_portfolio_plus_content AS a');
            $query -> join('LEFT','#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');
            $query -> join('LEFT','#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
            if(is_array($ids)){
                $query -> where('a.id IN('.implode(',',$ids).')');
            }else{
                if(is_string($ids) && strpos($ids, ',')){
                    $query -> where('a.id IN('.$ids.')');
                }else {
                    $query->where('a.id =' . ((int)$ids));
                }
            }
            $query -> group('id');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return false;
    }
}

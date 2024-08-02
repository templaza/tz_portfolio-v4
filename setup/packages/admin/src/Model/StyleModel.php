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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceTrait;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUser;

class StyleModel extends AddonModel
{
    use MVCFactoryServiceTrait;

    protected $type         = 'tz_portfolio-style';
    protected $folder       = 'styles';

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = Factory::getDbo();
        }
    }

    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',Factory::getApplication() -> input -> getInt('id'));

        $this -> setState('cache.filename', 'template_list');
    }

//    public function getTable($type = 'Extensions', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
//    {
//        return JTable::getInstance($type, $prefix, $config);
//    }

    public function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.'.$this -> getName(), $this -> getName(), array('control' => ''));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function afterSave($data){
        // Add template's information to table tz_portfolio_templates
        $tpl_data   = null;
        if(!$this -> getTemplateStyle($data['element'])){

            $lang   = Factory::getApplication() -> getLanguage();
            $tpl_data['title']      = $data['element'].' - '.Text::_('JDEFAULT');
            if(TZ_PortfolioTemplate::loadLanguage($data['element'])){
                if($lang -> hasKey('TZ_PORTFOLIO_TPL_'.$data['element'])){
                    $tpl_data['title']      = Text::_('TZ_PORTFOLIO_TPL_'.$data['element']).' - '.Text::_('JDEFAULT');
                }
            }
            $tpl_data['id']         = 0;
            $tpl_data['template']   = $data['element'];
            $tpl_data['home']       = 0;
            $tpl_data['params']     = '';

            $model  = $this -> getMVCFactory() -> createModel('Layout', 'Administrator');
            if($model){
                $model -> save($tpl_data);
            }
        }
        return true;
    }
    public function afterInstall($manifest)
    {
//        $result = parent::install();

        $style_name = (string) $manifest -> name;
        $style_path = COM_TZ_PORTFOLIO_STYLE_PATH.'/'.(string) $manifest -> name.'/config/default.json';
        if(file_exists($style_path)){
            $style  = $this -> getTemplateStyle($style_name);
            if(is_string($style -> params)){
                $style -> params    = json_decode($style -> params);
            }
            if(!isset($style -> layout) || (isset($style -> layout) && !$style -> layout)){
                $config = file_get_contents($style_path);
                $config = json_decode($config);
                if($config){
                    if(isset($config -> params) && $config -> params){
                        $defParams  = json_decode($config -> params);
                        $defParams  = array_intersect_key((array) $defParams, (array) $style -> params);
                        $itemParams = array_merge((array) $style -> params, $defParams);
                        $style -> params = (object) $itemParams;
                    }
                    if(isset($config -> layout) && $config -> layout) {
                        // Store layout
                        $db     = $this -> getDbo();
                        $query  = $db -> getQuery(true);
                        $query -> update('#__tz_portfolio_plus_templates');
                        $query -> set('layout='.$db -> quote($config->layout));
                        $query -> set('params='.$db -> quote(json_encode($style -> params)));
                        if(isset($style -> presets) && $style -> presets){
                            if(is_object($style -> presets)){
                                $query -> set('preset='.$db -> quote(json_encode($style -> presets)));
                            }else{
                                $query -> set('preset='.$db -> quote($style -> presets));
                            }
                        }
                        $query -> where('id='.$style -> id);
                        $db -> setQuery($query);
                        $db -> execute();
                    }
                }
            }
        }
    }

    public function getTemplateStyle($template){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_templates'));
        $query -> where($db -> quoteName('template').'='.$db -> quote($template));
        $query -> group($db -> quoteName('template'));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    public function uninstall($eid = array())
    {
        $user   = TZ_PortfolioUser::getUser();
        $app    = Factory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if (!$user->authorise('core.delete', 'com_tz_portfolio.style'))
        {
            Log::add(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), Log::WARNING, 'jerror');
            return false;
        }

        if (!is_array($eid))
        {
            $eid = array($eid => 0);
        }

        // Get an installer object for the extension type
        $table = $this -> getTable();

        $template_table     = $this -> getTable('Style');
        $template_default   = $template_table -> getHome();
//        $template_style     = JModelAdmin::getInstance('Template_Style','TZ_Portfolio_PlusModel',array('ignore_request' => true));
        $layout     = $this -> getMVCFactory() -> createModel('Layout', 'Administrator',
            array('ignore_request' => true));

        // Uninstall the chosen extensions
        $msgs = array();
        $result = false;

        foreach ($eid as $i => $id)
        {
            $id = trim($id);
            if($table -> load($id)){
                $langstring = 'COM_TZ_PORTFOLIO_' . strtoupper($table -> type);
                $rowtype = Text::_($langstring);

                if (strpos($rowtype, $langstring) !== false)
                {
                    $rowtype = $table -> type;
                }

                if ($table -> type && ($table -> type == $this -> type || $table -> type == 'tz_portfolio_plus-template'))
                {

                    // Is the template we are trying to uninstall a core one?
                    // Because that is not a good idea...
                    if ($table ->protected)
                    {
                        Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_WARNCORETEMPLATE',
                            Text::_('COM_TZ_PORTFOLIO_'.$view)), Log::WARNING, 'jerror');
                        return false;
                    }

                    if($template_default -> template == $table -> element){
                        $msg    = Text::_('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_TEMPLATE_DEFAULT');
                        $app->enqueueMessage($msg,'warning');
                        return false;
                    }

                    $tpl_path   = COM_TZ_PORTFOLIO_STYLE_PATH.DIRECTORY_SEPARATOR.$table -> element;

                    if(is_dir($tpl_path)){
                        if(!$layout -> deleteTemplate($table -> name)){
                            $app -> enqueueMessage($layout -> getError(),'warning');
                            return false;
                        }
                        if(Folder::delete($tpl_path)){
                            if(!preg_match('/^tz_portfolio_plus/', $table -> type)
                                ||(preg_match('/^tz_portfolio_plus/', $table -> type) &&
                                !file_exists(JPATH_ADMINISTRATOR
                                    .'/components/com_tz_portfolio_plus/tz_portfolio_plus.xml'))) {
                                $result = $this->delete($id);
                            }
                        }
                    }

                    // Build an array of extensions that failed to uninstall
                    if ($result === false)
                    {
                        // There was an error in uninstalling the package
                        $msgs[] = Text::sprintf('COM_TZ_PORTFOLIO_UNINSTALL_ERROR', Text::_('COM_TZ_PORTFOLIO_'.$view));
                        $result = false;
                    }
                    else
                    {
                        // Package uninstalled sucessfully
                        $msgs[] = Text::sprintf('COM_TZ_PORTFOLIO_UNINSTALL_SUCCESS', Text::_('COM_TZ_PORTFOLIO_'.$view));
                        $result = true;
                    }
                }
            }else
            {
                $this->setError($table->getError());

                return false;
            }
        }

        $msg = implode("<br />", $msgs);
        $app->enqueueMessage($msg);

        return $result;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.template');
            }
            return $state;
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing group.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && $record -> asset_id) {
                $state = $user->authorise('core.edit.state', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.edit.state', $this->option . '.template');
            }
            return $state;
        }

        return parent::canEditState($record);
    }

    public function getUrlFromServer($xmlTag = 'templateurl'){
        return parent::getUrlFromServer($xmlTag);
    }

    protected function getManifest_Cache($element, $folder = null, $type = 'tz_portfolio-style', $key = null){
        return parent::getManifest_Cache($element, $folder, $type, $key);
    }

    protected function __get_extensions_installed(&$update = array(), $model_type = 'Styles',
                                                  $model_prefix = 'Administrator', &$limit_start = 0){
        return parent::__get_extensions_installed($update, $model_type, $model_prefix, $limit_start);
    }
}
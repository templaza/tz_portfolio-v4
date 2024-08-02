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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use stdClass;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonsHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

class AddonController extends FormController
{

//    public function __construct($config = array()){
//        parent::__construct($config);
//    }
//    public function display($cachable = false, $urlparams = false)
//    {
//        parent::display($cachable,$urlparams);
//    }

    public function manager(){
        $app   = Factory::getApplication();
        $model = $this->getModel();
        $table = $model->getTable();
        $cid    = array();
        $context = "$this->option.edit.$this->context";
        $this -> input -> set('layout','manager');

        $addon_view     = $this -> input -> getCmd('addon_view');
        $addon_task     = $this -> input -> getCmd('addon_task');
        $addon_layout   = $this -> input -> getCmd('addon_layout');

        $link           = '';
        if($addon_view){
            $link   .= '&addon_view='.$addon_view;
        }
        if($addon_task){
            $link   .= '&addon_task='.$addon_task;
        }
        if($addon_layout){
            $link   .= '&addon_layout='.$addon_layout;
        }

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
        $checkin = property_exists($table, 'checked_out');

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key))
        {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend().$link, false
                )
            );

            return false;
        }

        // Attempt to check-out the new record for editing and redirect.
        if ($checkin && !$model->checkout($recordId))
        {
            // Check-out failed, display a notice but allow the user to see the record.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return false;
        }
        else
        {
            // Check-out succeeded, push the new record id into the session.
            $this->holdEditId($context, $recordId);
            $app->setUserState($context . '.data', null);


            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return true;
        }
    }

    public function upload()
    {
        // Check for request forgeries.
        $this -> checkToken();

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Redirect to the edit screen.
        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
        // Check for request forgeries.
        $this -> checkToken();
//        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        $model  = $this -> getModel();
        if(!$model -> install()){
            $this -> setMessage($model -> getError(), 'error');
        }else{
            $this -> setMessage(Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_SUCCESS',
                Text::_('COM_TZ_PORTFOLIO_'.strtoupper($this -> view_item))));
        }

        $this -> setRedirect('index.php?option=com_tz_portfolio&view='.$this -> view_item.'&layout=upload');
    }

    public function uninstall(){

        // Check for request forgeries.
        $this -> checkToken();

        $eid   = $this->input->get('cid', array(), 'array');
        $model = $this->getModel();

        $eid    = ArrayHelper::toInteger($eid);
        $model->uninstall($eid);
        $this->setRedirect(Route::_('index.php?option=com_tz_portfolio&view=addons', false));
    }

    public function cancel($key = null)
    {
        $this -> checkToken();

        $cancel = parent::cancel($key);

        $app    = Factory::getApplication();
        $app -> setUserState($this->option . '.'.$this -> context.'.limitstart', 0);

        if($return = $this -> input -> get('return', null, 'base64')){
            $this -> setRedirect(base64_decode($return));
            return true;
        }

        return $cancel;
    }

    public function save($key = null, $urlVar = null)
    {
        $user   = Factory::getUser();

        $data   = $this->input->get('jform', array(), 'array');

        // Remove the permissions rules data if user isn't allowed to edit them.
        if (!$user->authorise('core.admin', 'com_tz_portfolio.addon')
            && isset($data['params']) && isset($data['params']['rules']))
        {
            unset($data['params']['rules']);
        }

        if (parent::save($key, $urlVar)) {
            if($return = $this->input->get('return', null, 'base64')){
                $task   = $this->getTask();
                $model  = $this->getModel();
                $table  = $model->getTable();

                // Determine the name of the primary key for the data.
                if (empty($key))
                {
                    $key = $table->getKeyName();
                }

                // To avoid data collisions the urlVar may be different from the primary key.
                if (empty($urlVar))
                {
                    $urlVar = $key;
                }

                $recordId = $this->input->getInt($urlVar);

                switch ($task)
                {
                    case 'apply':
                        // Redirect back to the edit screen.
                        $this->setRedirect(
                            Route::_(
                                'index.php?option=' . $this->option . '&view=' . $this->view_item
                                . $this->getRedirectToItemAppend($recordId, $urlVar).'&return='.$return, false
                            )
                        );
                        break;
                    case 'save':
                        $this->setRedirect(base64_decode($return));
                        break;
                    default:
                        break;
                }
            }
            return true;
        }
        return false;
    }

    protected function allowAdd($data = array())
    {
//        $user = TZ_Portfolio_PlusUser::getUser();
        $user = Factory::getUser();
        return ($user->authorise('core.create','com_tz_portfolio.'.$this -> getName()));
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $user       = Factory::getUser();
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
        $tblAsset   = Table::getInstance('Asset','Table');

        // Return the addon edit options permission
        if($recordId){
            return $user->authorise('core.edit', 'com_tz_portfolio.addon.'.$recordId)
                || $user->authorise('core.admin', 'com_tz_portfolio.addon.'.$recordId)
                || $user->authorise('core.options', 'com_tz_portfolio.addon.'.$recordId);
        }

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        if($tblAsset -> loadByName('com_tz_portfolio.addon.'.$recordId)) {
            return $user->authorise('core.edit', $this->option . '.addon.'.$recordId);
        }
        return $user->authorise('core.edit', $this->option . '.addon');
    }

    public function edit($key = null, $urlVar = null)
    {
        // Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
        Factory::getApplication()->allowCache(false);

        $model = $this->getModel();
        $table = $model->getTable();
        $cid = $this->input->post->get('cid', array(), 'array');

        // Determine the name of the primary key for the data.
        if (empty($key)) {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar)) {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int)(count($cid) ? $cid[0] : $this->input->getInt($urlVar));

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key)) {
            $this->setMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        return parent::edit($key, $urlVar);
    }

    public function ajax_install()
    {
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        $result = null;
        $app    = Factory::getApplication();


        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
        }else{
            $model  = $this -> getModel();
            if($result = $model -> install()){
                $app -> enqueueMessage(Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_SUCCESS'
                    , Text::_('COM_TZ_PORTFOLIO_'.strtoupper($this -> view_item))));
            }else{
                $this -> setMessage($model -> getError());
                $app -> enqueueMessage($model -> getError(), 'error');
            }
        }

        $message = $this->message;

        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        $redirect   = $this -> redirect;

        // Push message queue to session because we will redirect page by Javascript, not $app->redirect().
        // The "application.queue" is only set in redirect() method, so we must manually store it.
        $app->getSession()->set('application.queue', $app->getMessageQueue());


        header('Content-Type: application/json');

        echo new JsonResponse(array('redirect' => $redirect), $message, !$result);

        exit();
    }
}

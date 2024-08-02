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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

//HTMLHelper::_('bootstrap.tooltip');
//HTMLHelper::_('behavior.formvalidator');
//HTMLHelper::_('behavior.keepalive');

//$this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs.min.css', array('version' => 'v=2.9.3'));
//$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/vendor/intro/intro.min.js', array('version' => 'v=2.9.3'));
//$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/js/introguide.min.js', array('version' => 'v=2.9.3'));
//
//if(Factory::getApplication() -> getLanguage() -> isRtl()) {
//    $this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs-rtl.min.css', array('version' => 'v=2.9.3'));
//}
//
//$this -> document -> addScriptDeclaration('
//(function($,window){
//    "use strict";
//    
//    $(document).ready(function(){
//        var addonSteps  = [
//                {
//                    /* Step 1: Upload */
//                    element: $("[data-target=\\"#tpp-addon__upload\\"]")[0],
//                    intro: "<div class=\\"head\\">'.$this -> escape(JText::_('JTOOLBAR_UPLOAD')).'</div>'
//                        .$this -> escape(JText::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_UPLOAD_MANUAL_DESC', JText::_('COM_TZ_PORTFOLIO_ADDON'))).'",
//                    position: "right"
//                },
//                {
//                    /* Step 2: Install online */
//                    element: $(".action-links .install-now")[0],
//                    intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_INSTALL_UPDATE_ONLINE')).'</div>'
//                        .$this -> escape(JText::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_INSTALL_UPDATE_ONLINE_DESC', JText::_('COM_TZ_PORTFOLIO_ADDON'))).'",
//                    position: "top"
//                }];
//                
//        if($(".action-links .js-tpp-live-demo").length){
//            addonSteps[2]   = {
//                /* Step 3: Demo link */
//                element: $(".action-links .js-tpp-live-demo")[0],
//                intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_LIVE_DEMO')).'</div>'
//                    .$this -> escape(JText::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_LIVE_DEMO_DESC')).'",
//                position: "top"
//            }
//        }
//        
//        tppIntroGuide("'.$this -> getName().'",addonSteps , '.(TZ_Portfolio_PlusHelper::introGuideSkipped($this -> getName())?1:0).', "'.JSession::getFormToken().'");
//        
//    });
//     
//     
//})(jQuery,window);
//');


/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa -> useStyle('com_tz_portfolio.introjs');

if($lang -> isRtl()){
    $wa -> useStyle('com_tz_portfolio.intro-rtl');
}

$wa -> addInlineScript('(function($){
    "use strict";
    $(document).ready(function(){
    
        var addonSteps  = [
        {
            /* Step 1: Upload */
            element: $("[data-target=\\"#tpp-addon__upload\\"]")[0],
            title: "' . $this->escape(Text::_('JTOOLBAR_UPLOAD')). '",
            intro: "' . $this->escape(Text::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_UPLOAD_MANUAL_DESC',
                Text::_('COM_TZ_PORTFOLIO_ADDON'))) . '",
            position: "right"
        },
        {
            /* Step 2: Install online */
            element: $(".action-links .install-now")[0],
            title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_INSTALL_UPDATE_ONLINE')). '",
            intro: "' . $this->escape(Text::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_UPLOAD_MANUAL_DESC',
        Text::_('COM_TZ_PORTFOLIO_ADDON'))) . '",
            position: "top"
        }];
                
        if($(".action-links .js-tpp-live-demo").length){
            addonSteps.push({
                /* Step 3: Demo link */
                element: $(".action-links .js-tpp-live-demo")[0],
                title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_LIVE_DEMO')). '",            
                intro: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_LIVE_DEMO_DESC')) . '",
                position: "top"
            });
        }
    
        tppIntroGuide("'.$this -> getName().'",addonSteps , '
    .(TZ_PortfolioHelper::introGuideSkipped($this -> getName())?1:0).', "'.Session::getFormToken().'");
    });
})(jQuery);', ['position' => 'after'], [], ['com_tz_portfolio.introguide']);
?>

<?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <?php echo HTMLHelper::_('tzbootstrap.startcontainer', '10', false);?>
    <form name="adminForm" method="post" id="adminForm" class="tpp-extension__upload"
          enctype="multipart/form-data"
          action="index.php?option=com_tz_portfolio&view=style&layout=upload">

        <?php echo $this -> loadTemplate('list'); ?>

        <input type="hidden" value="" name="task">
        <?php echo HTMLHelper::_('form.token');?>
    </form>

    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>
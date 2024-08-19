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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Mediatype\Image\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Psr\Container\ContainerInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOn;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

defined('_JEXEC') or die;

/**
 * Field Image Add-On
 */
class Image extends AddOn
{
    protected $autoloadLanguage = true;

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param   ContainerInterface  $container  The container
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function boot(ContainerInterface $container)
    {
        $this -> registerStyle();
    }
    private function registerStyle(){
        $wa = Factory::getDocument()->getWebAssetManager();

        $wa->registerStyle('com_tz_portfolio.addon.'.$this -> _type.'.'.$this -> _name.'.style',
            TZ_PortfolioUri::base(true). '/addons/'.$this -> _type.'/'.$this -> _name.'/css/style.css');
    }

    /**
     * @var Form $form
     * @var CMSObject $data
     */
    public function onContentPrepareForm($form, $data)
    {
        $app    = Factory::getApplication();
        $result = parent::onContentPrepareForm($form, $data); // TODO: Change the autogenerated stub

        $is_myaddon = false;

        if(isset($data -> element) && isset($data -> folder)){
            $is_myaddon = $this -> _type == $data -> folder && $this -> _name == $data -> element;
        }

        if($app -> isClient('administrator')
            && $form -> getName() == 'com_tz_portfolio.addon' && $is_myaddon) {

            /* @var FormField $field */
            $field      = $form -> getField('mt_image_watermark_fontpath', 'params');

//            if(!$field){
//                return $result;
//            }

            $properties = is_object($data)?$data -> getProperties():array();

            if(empty($properties)){
                return $result;
            }

            if(!empty($properties) && isset($properties['params']['mt_image_watermark_fontpath'])){
                $value  = $properties['params']['mt_image_watermark_fontpath'];
            }

            $value  = empty($value)?$field -> __get('value'):$value;
            $value  = empty($value)?$field -> getAttribute('default'):$value;

            if(!empty($value) && strpos($value,'com_tz_portfolio_plus') !== false) {
                $value = str_replace('administrator/components/com_tz_portfolio_plus',
                    COM_TZ_PORTFOLIO_MEDIA_BASE, $value);

                if(is_object($data)){
                    $properties['params']['mt_image_watermark_fontpath']  = $value;
                    $data -> setProperties($properties);
                }
            }
        }

        return $result;
    }

    // Display html for views in front-end.
    public function onContentDisplayMediaType($context, &$article, $params, $page = 0, $layout = null){
        if($article){
            if($media = $article -> media){
                $image  = null;
                $image_properties = null;
                if(isset($media -> image)){
                    $image  = clone($media -> image);
                    if(isset($image -> url) && $image -> url) {
                        if ($size = $params->get('mt_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                if (file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url = Uri::base(true) .'/'. $image_url;
                            }

                            if (isset($image->url_detail) && !empty($image->url_detail)) {
                                $image_url_ext = File::getExt($image->url_detail);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_detail);
                                $image->url_detail = Uri::base(true) . '/' . $image_url;
                            }
                        }
                    }
                }
                $this -> setVariable('image', $image);
                $this -> setVariable('image_properties', $image_properties);
            }
            $this -> setVariable('item', $article);

            return parent::onContentDisplayMediaType($context, $article, $params, $page, $layout);
        }
    }
}
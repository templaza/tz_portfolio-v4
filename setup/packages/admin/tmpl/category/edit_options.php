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

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_('bootstrap.startAccordion', 'categoryOptions', array('active' => 'collapse0', 'parent' => true));
?>
<?php echo HTMLHelper::_('bootstrap.addSlide', 'categoryOptions', Text::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>

    <div class="control-group">
        <div class="control-label">
            <?php echo $this->form->getLabel('created_user_id'); ?>
        </div>
        <div class="controls">
            <?php echo $this->form->getInput('created_user_id'); ?>
        </div>
    </div>
<?php if (intval($this->item->created_time)) : ?>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this->form->getLabel('created_time'); ?>
        </div>
        <div class="controls">
            <?php echo $this->form->getInput('created_time'); ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($this->item->modified_user_id) : ?>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this->form->getLabel('modified_user_id'); ?>
        </div>
        <div class="controls">
            <?php echo $this->form->getInput('modified_user_id'); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this->form->getLabel('modified_time'); ?>
        </div>
        <div class="controls">
            <?php echo $this->form->getInput('modified_time'); ?>
        </div>
    </div>
<?php endif; ?>
<?php echo HTMLHelper::_('bootstrap.endSlide');?>

<?php
$form       = $this -> form;
$xml        = $form -> getXml();
$fieldSets  = $form -> getFieldsets('params');

$attribsFieldSet = $form->getFieldsets('attribs');

$fieldSets  = array_merge($fieldSets, $attribsFieldSet);

$i          = 1;
$opentab    = 0;

foreach ($fieldSets as $name => $fieldSet) :
    $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_TZ_PORTFOLIO_'.$name.'_FIELDSET_LABEL';

    $fields	= $form->getFieldset($name);

    if($name == 'basic' && $fields && count($fields) <= 1 ){
        continue;
    }

    $hasChildren    = $xml->xpath('//fieldset[@name="' . $name . '"]/fieldset');
    $hasParent      = $xml->xpath('//fieldset/fieldset[@name="' . $name . '"]');
    $isGrandchild   = $xml->xpath('//fieldset/fieldset/fieldset[@name="' . $name . '"]');

//        var_dump($name);
//        var_dump('$hasChildren');
//        var_dump((bool) $hasChildren);
//        var_dump('$hasParent');
//        var_dump((bool) $hasParent);

    if(!$hasParent){
        if ($opentab){
            echo HTMLHelper::_('bootstrap.endSlide');
        }

        echo HTMLHelper::_('bootstrap.addSlide', 'categoryOptions', Text::_($label), 'collapse' . $i++);

        $opentab = 1;
    }
    if (isset($fieldSet->description) && trim($fieldSet->description)) :
        echo '<p class="tip">'.$this->escape(Text::_($fieldSet->description)).'</p>';
    endif;
    ?>
    <fieldset<?php echo (!$isGrandchild && $hasParent)?' class="options-form"':''?> >
        <?php if (!$isGrandchild && $hasParent){ ?>
            <legend><?php echo Text::_($fieldSet->label); ?></legend>
        <?php }?>
        <?php
        if(!$hasChildren) {
            foreach ($fields as $field) {
                echo $field->renderField();
            }
            $opentab = 2;
        }?>
    </fieldset>
<?php
//            if($opentab) {
//                echo HTMLHelper::_('bootstrap.endSlide');
//            }
endforeach;
if($opentab) {
    echo HTMLHelper::_('bootstrap.endSlide');
}
echo HTMLHelper::_('bootstrap.endAccordion');
?>
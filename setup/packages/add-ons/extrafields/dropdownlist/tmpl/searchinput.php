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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

if ($options):
    $params = $this -> params;
    if($params -> get('search_type', 'dropdownlist') == 'dropdownlist'
        || $params -> get('search_type', 'dropdownlist') == 'multiselect') {
        if($params -> get('show_label', 1)) {
        ?>
        <label class="form-label"><?php echo $this->getTitle(); ?></label>
        <?php }
        $this->setAttribute("class", 'form-select', "search");
        echo HTMLHelper::_('select.genericlist', $options, $this->getSearchName(), $this->getAttribute(null, null, "search"), 'value', 'text', $value, $this->getSearchId());
    }else{
?>
    <fieldset id="<?php echo $this -> getSearchId();?>" class="checkboxes <?php echo $this -> getInputClass();?>">
        <?php if($params -> get('show_label', 1)){?>
        <label class="form-label"><?php echo $this -> getTitle();?></label>
        <?php }?>
        <ul class="list-unstyled list-extrafield mb-0">
            <?php foreach ($options AS $key => $option){
                if ($option->text == strtoupper($option->text))
                {
                    $text = JText::_($option->text);
                }
                else
                {
                    $text = $option->text;
                }

                $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "search");

                if (is_array($value) && in_array($option->value, $value))
                {
                    $this->setAttribute("checked", "checked", "search");
                }
                elseif($option->value === $value)
                {
                    $this->setAttribute('checked', 'checked', 'search');
                }else{
                    $this->setAttribute('checked', null, 'search');
                }

                if (isset($option->disabled) && $option->disabled)
                {
                    $this->setAttribute('disabled', 'disabled', 'search');
                }
                else
                {
                    $this->setAttribute('disabled', null, 'search');
                }
                ?>
                <li>
                    <label class="checkbox" for="<?php echo $this -> getSearchId().$key;?>">
                        <input id="<?php echo $this -> getSearchId().$key;?>" name="<?php echo $this -> getSearchName();?>"<?php
                        echo $this->getAttribute(null, null, "search");
                        ?>/> <?php echo $text;?></label>
                </li>
            <?php }?>
        </ul>
    </fieldset>
<?php
    }
endif;
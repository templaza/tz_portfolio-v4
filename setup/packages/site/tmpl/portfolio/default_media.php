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

if($item = $this -> item) {
    if($item->event->onContentDisplayMediaType && !empty($item->event->onContentDisplayMediaType)){
?>
<div class="tpp-portfolio__media uk-card-media-top">
    <?php echo $item->event->onContentDisplayMediaType; ?>
</div>
<?php }
}
?>
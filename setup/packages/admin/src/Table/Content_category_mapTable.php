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

use Joomla\CMS\Table\Table;

// no direct access
defined('_JEXEC') or die;

class Content_Category_MapTable extends Table
{
    public function __construct($db)
    {
        parent::__construct('#__tz_portfolio_content_category_map', 'id', $db);
    }

    public function resetAll()
    {
        // Get the default values for the class from the table.
        foreach ($this->getFields() as $k => $v)
        {
            // If the property is not the primary key or private, reset it.
            if ((strpos($k, '_') !== 0))
            {
                $this->$k = $v->Default;
            }
        }

        // Reset table errors
        $this->_errors = array();
    }
}
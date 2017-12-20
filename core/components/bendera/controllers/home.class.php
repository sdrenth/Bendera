<?php
/**
 * Bendera
 *
 * Copyright 2010 by Shaun McCormick <shaun+bendera@modx.com>
 *
 * Bendera is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Bendera is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Bendera; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package bendera
 * @subpackage controllers
 */
/**
 * Loads the home page.
 *
 * @package bendera
 * @subpackage controllers
 */

require_once dirname(__DIR__) . '/index.class.php';

/**
 * Loads the home page.
 *
 * @package formit
 * @subpackage controllers
 */
class BenderaHomeManagerController extends BenderaBaseManagerController
{
    public function process(array $scriptProperties = array())
    {

    }
    public function getPageTitle()
    {
        return $this->modx->lexicon('bendera');
    }

    public function loadCustomCssJs()
    {
        $this->addJavascript($this->bendera->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->bendera->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->bendera->config['jsUrl'] . 'mgr/widgets/superboxselect.js');
        $this->addLastJavascript($this->bendera->config['jsUrl'] . 'mgr/sections/home.js');
    }

    public function getTemplateFile()
    {
        return $this->bendera->config['templatesPath'] . 'home.tpl';
    }
}

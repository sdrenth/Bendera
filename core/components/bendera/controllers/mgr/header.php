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
 */
/**
 * Loads the header for mgr pages.
 *
 * @package bendera
 * @subpackage controllers
 */

/*
 * Retrieve templates.
 */
$templates = $modx->getCollection('modTemplate');
foreach ($templates as $template) {
    $templateList .= "['".$template->get('id')."', '".htmlspecialchars($template->get('templatename'),ENT_QUOTES)."'],";
}

/*
 * Retrieve contexts.
 */
$contexts = $modx->getCollection('modContext');
$contextsList = array();

if($contexts) {
    foreach ($contexts as $context) {
        if($context->get('key') === 'mgr') continue;

        $contextsList[] = array(
            'title' => $context->get('name'),
            'xtype' => 'bendera-grid-items',
            'id' => $context->get('key'),
            'preventRender' => true,
            'items' => array(
                'html' => '',
                'border' => false
            )
        );
    }
}

/**
 * Reg all scripts.
 */
$modx->regClientCSS($Bendera->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($Bendera->config['jsUrl'].'mgr/bendera.js');
$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/util/datetime.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Bendera.config = '.$modx->toJSON($Bendera->config).';
    Bendera.config.connector_url = "'.$Bendera->config['connectorUrl'].'";
    Bendera.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
    Bendera.contexts = '.$modx->toJSON($contextsList).';
    Bendera.templateList = {
        templates: ['.trim($templateList, ','). ']
    };
});
</script>');

return '';

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

$depth = 2;

$c = $modx->newQuery('modResource');
$c->where(array(
    'deleted' => false,
    'published' => true,
    'parent' => 0,
    'context_key' => 'web'
));
$rootResources = $modx->getCollection('modResource', $c);
foreach ($rootResources as $parent) {
    $children = $modx->getChildIds($parent->get('id'), $depth, array('context' => 'web'));

    $resourceListWeb .=  "['".$parent->get('id')."', '".htmlspecialchars($parent->get('pagetitle'), ENT_QUOTES);
    $resourceListWeb .= " (".$parent->get('id').")'],";

    foreach ($children as $child) {
        $resource = $modx->getObject('modResource', $child);

        $resourceListWeb .= "['".$resource->get('id')."', '".htmlspecialchars($resource->get('pagetitle'), ENT_QUOTES);
        $resourceListWeb .= " (".$resource->get('id').")'],";
    }
}

$c = $modx->newQuery('modResource');
$c->where(array(
    'deleted' => false,
    'published' => true,
    'parent' => 0,
    'context_key' => 'de'
));
$rootResources = $modx->getCollection('modResource', $c);
foreach ($rootResources as $parent) {
    $children       = $modx->getChildIds($parent->get('id'), $depth, array('context' => 'de'));

    $resourceListDE .= "['".$parent->get('id')."', '".htmlspecialchars($parent->get('pagetitle'), ENT_QUOTES)." (".$parent->get('id').")'],";

    foreach ($children as $child) {
        $resource = $modx->getObject('modResource', $child);
        $resourceListDE .= "['".$resource->get('id')."', '".htmlspecialchars($resource->get('pagetitle'),ENT_QUOTES)." (".$resource->get('id').")'],";

    }
}

/*
 * Only retrieve templates from category 0. Other templates are Sterc Only or Newsletter templates.
 */
$c = $modx->newQuery('modTemplate');
$c->where(
    array(
        'category' => 0,
    )
);
$templates = $modx->getCollection('modTemplate', $c);
foreach ($templates as $template) {
    $templateList .= "['".$template->get('id')."', '".htmlspecialchars($template->get('templatename'),ENT_QUOTES)."'],";
}

$modx->regClientCSS($Bendera->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($Bendera->config['jsUrl'].'mgr/bendera.js');
$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/util/datetime.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Bendera.config = '.$modx->toJSON($Bendera->config).';
    Bendera.config.connector_url = "'.$Bendera->config['connectorUrl'].'";
    Bendera.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
    Bendera.templateList = {
        templates: ['.trim($templateList, ','). ']
    };
});
</script>');

/*
 Bendera.resourceList = {
        web: ['.trim($resourceListWeb,',').'],
        de: ['.trim($resourceListDE,',').'],
    }
*/

return '';

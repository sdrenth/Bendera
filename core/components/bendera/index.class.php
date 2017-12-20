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

require_once __DIR__ . '/model/bendera/bendera.class.php';

abstract class BenderaBaseManagerController extends modExtraManagerController
{
    public $bendera;

    public function initialize()
    {
        $this->bendera = new Bendera($this->modx);

        /*
         * Retrieve templates.
         */
        $templates = $this->modx->getCollection('modTemplate');
        foreach ($templates as $template) {
            $templateList .= "['".$template->get('id')."', '".htmlspecialchars($template->get('templatename'),ENT_QUOTES)."'],";
        }

        /*
         * Retrieve contexts.
         */
        $contexts     = $this->modx->getCollection('modContext');
        $contextsList = array();

        if ($contexts) {
            foreach ($contexts as $context) {
                $excludedContexts = explode(',', $this->modx->getOption('bendera.exclude_contexts'));
                $excludedContexts = array_merge($excludedContexts, array('mgr'));

                if (in_array($context->get('key'), $excludedContexts)) {
                    continue;
                }

                $contextsList[] = array(
                    'title'         => $context->get('name'),
                    'xtype'         => 'bendera-grid-items',
                    'id'            => $context->get('key'),
                    'preventRender' => true,
                    'items'         => array(
                        'html'   => '',
                        'border' => false
                    )
                );
            }
        }

        /**
         * Reg all scripts.
         */
        $this->addCss($this->bendera->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->bendera->config['jsUrl'] . 'mgr/bendera.js');
        $this->addJavascript($this->modx->getOption('manager_url') . 'assets/modext/util/datetime.js');
        $this->addHtml(
            '<script type="text/javascript">
            Ext.onReady(function() {
                Bendera.config = '.$this->modx->toJSON($this->bendera->config).';
                Bendera.config.connector_url = "'.$this->bendera->config['connectorUrl'].'";
                Bendera.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
                Bendera.contexts = '.$this->modx->toJSON($contextsList).';
                Bendera.templateList = {
                    templates: ['.trim($templateList, ','). ']
                };
            });
            </script>'
        );

        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('bendera:default');
    }

    public function checkPermissions()
    {
        return true;
    }
}

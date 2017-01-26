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
 * The base class for Bendera.
 *
 * @package bendera
 */
class Bendera
{
    /**
     * Bendera constructor.
     *
     * @param modX  $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption(
            'bendera.core_path',
            $config,
            $this->modx->getOption('core_path').'components/bendera/'
        );
        $assetsUrl = $this->modx->getOption(
            'bendera.assets_url',
            $config,
            $this->modx->getOption('assets_url').'components/bendera/'
        );
        $connectorUrl = $assetsUrl.'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'images/',
            'connectorUrl' => $connectorUrl,
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
        ), $config);

        $this->modx->addPackage('bendera', $this->config['modelPath']);
        $this->modx->lexicon->load('bendera:default');
    }

    /**
     * Initializes Bendera into different contexts.
     *
     * @access public
     *
     * @param string $ctx The context to load. Defaults to web.
     *
     * @return bool|string
     */
    public function initialize($ctx = 'web')
    {
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass(
                    'bendera.request.BenderaControllerRequest',
                    $this->config['modelPath'],
                    true,
                    true
                )) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new BenderaControllerRequest($this);

                return $this->request->handleRequest();
            break;
            case 'connector':
                if (!$this->modx->loadClass(
                    'bendera.request.BenderaConnectorRequest',
                    $this->config['modelPath'],
                    true,
                    true
                )) {
                    return 'Could not load connector request handler.';
                }
                $this->request = new BenderaConnectorRequest($this);

                return $this->request->handle();
            break;
            default:
                /* if you wanted to do any generic frontend stuff here.
                 * For example, if you have a lot of snippets but common code
                 * in them all at the beginning, you could put it here and just
                 * call $bendera->initialize($modx->context->get('key'));
                 * which would run this.
                 */
            break;
        }
    }
}
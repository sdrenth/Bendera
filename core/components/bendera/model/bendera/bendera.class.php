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

        $connectorUrl = $assetsUrl . 'connector.php';

        $this->modx->lexicon->load('bendera:default');

        $this->config = array_merge(
            array(
                'corePath'        => $corePath,
                'assetsUrl'       => $assetsUrl,
                'cssUrl'          => $assetsUrl . 'css/',
                'jsUrl'           => $assetsUrl . 'js/',
                'imagesUrl'       => $assetsUrl . 'images/',
                'controllersPath' => $corePath . 'controllers/',
                'connectorUrl'    => $connectorUrl,
                'modelPath'       => $corePath . 'model/',
                'chunksPath'      => $corePath . 'elements/chunks/',
                'chunkSuffix'     => '.chunk.tpl',
                'snippetsPath'    => $corePath . 'elements/snippets/',
                'templatesPath'   => $corePath . 'templates/',
                'processorsPath'  => $corePath . 'processors/',
                'types'           => $this->getTypes(),
            ),
            $config
        );

        $this->modx->addPackage('bendera', $this->config['modelPath']);
    }

    /**
     * Return available Bendera types.
     *
     * @return array
     */
    public function getTypes()
    {
        $types        = array();
        $defaultTypes = array(
            'button',
            'banner',
            'html',
            'image',
            'affiliate'
        );

        $allowedTypes = str_replace(' ', '', $this->modx->getOption('bendera.allowed_types'));
        if (!empty($allowedTypes)) {
            $allowedTypesArr = explode(',', $allowedTypes);

            foreach ($defaultTypes as $key => $type) {
                if (!in_array($type, $allowedTypesArr)) {
                    unset($defaultTypes[$key]);
                }
            }
        }

        foreach($defaultTypes as $type) {
            $types[] = array(
                $type,
                $this->modx->lexicon('bendera.type.' . $type)
            );
        }

        return $types;
    }

    /**
     * @param $scriptProperties
     *
     * @return string
     */
    public function getBenderaItems($scriptProperties)
    {
        $ids           = $this->modx->getOption('ids', $scriptProperties, null);
        $limit         = $this->modx->getOption('limit', $scriptProperties, 1);
        $sortBy        = $this->modx->getOption('sortBy', $scriptProperties, 'startdate');
        $sortDir       = $this->modx->getOption('sortDir', $scriptProperties, 'ASC');
        $toPlaceholder = $this->modx->getOption('toPlaceholder', $scriptProperties, false);
        $types         = $this->modx->getOption('types', $scriptProperties, null);
        $contexts      = $this->modx->getOption('contexts', $scriptProperties, null);

        /* Tpls. */
        $htmlTpl      = $this->modx->getOption('htmlTpl', $scriptProperties, 'benderaItemHTML');
        $buttonTpl    = $this->modx->getOption('buttonTpl', $scriptProperties, 'benderaItemButton');
        $imageTpl     = $this->modx->getOption('imageTpl', $scriptProperties, 'benderaItemImage');
        $affiliateTpl = $this->modx->getOption('affiliateTpl', $scriptProperties, 'benderaItemAffiliate');
        $wrapper      = $this->modx->getOption('wrapperTpl', $scriptProperties, 'benderaWrapper');

        $template = ($this->modx->resource) ? $this->modx->resource->get('template') : null;
        $resource = ($this->modx->resource) ? $this->modx->resource->get('id') : null;
        $date     = date('Y-m-d H:i:s');

        $c = $this->modx->newQuery('BenderaItem');
        $c->select('id,title,description,content,size,startdate,enddate,type,resource,categories,createdon,context,link_internal,link_external');
        $c->sortby($sortBy, $sortDir);

        $where = array(
            'active' => 1
        );

        $useDates = (bool) $this->modx->getOption('bendera.use_dates');
        if ($useDates) {
            $where[] = array(
                'startdate:<=' => $date,
                'enddate:>='   => $date
            );
        }

        if ($contexts) {
            $where[] = array('context:IN' => explode(',', $contexts));
        }

        if ($types) {
            $where[] = array('type:IN' => explode(',', $types));
        }

        if (isset($ids)) {
            $where[] = array('id:IN' => explode(',', $ids));
        }

        $c->where($where);
        $c->limit($limit);
        $c->prepare();
        $query = $c->toSQL();

        $filter  = "AND (`categories` = '' OR FIND_IN_SET(" . $template . ", `categories`) > 0)";
        $filter .= "AND (`resource` = '' OR FIND_IN_SET(" . $resource . ", `resource`) > 0)";
        $filter .= " ORDER BY";

        $query   = str_replace('ORDER BY', $filter, $query);
        $results = $this->modx->query($query);
        $rows    = '';
        if ($results) {
            while ($item = $results->fetch(PDO::FETCH_ASSOC)) {
                $rows .= $this->modx->getChunk($tpl, $item);
            }
        }

        if (!empty($wrapper)) {
            $output = $this->modx->getChunk($wrapper, array('output' => $rows));
        } else {
            $output = $rows;
        }

        if (!empty($toPlaceholder)) {
            /* if using a placeholder, output nothing and set output to specified placeholder */
            $this->modx->setPlaceholder($toPlaceholder, $output);
            return '';
        }

        /* by default just return output */
        return $output;
    }
}

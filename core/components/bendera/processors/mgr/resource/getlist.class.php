<?php
/**
 * Gets a list of resources.
 *
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 * @return array An array of modResources
 *
 * @package modx
 * @subpackage processors.resource
 */
class BenderamodResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('resource');
    public $defaultSortField = 'pagetitle';
    public $permission = 'view';

    public $contextNames = array();

    function __construct(modX & $modx,array $properties = array())
    {
        parent::__construct($modx, $properties);

        $ctxs = $this->modx->getIterator('modContext');
        foreach ($ctxs as $ctx) {
            $this->contextNames[$ctx->get('key')] = $ctx->get('name');
        }
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $conditions = $this->getProperties();

        if (!empty($conditions['query'])) {
            $where['pagetitle:LIKE'] = '%'.$conditions['query'].'%';
        }

        $c->where($where);

        return $c;
    }

    public function prepareRow($object)
    {
        if ($object) {
            $charset = $this->modx->getOption('modx_charset', null, 'UTF-8');
            $objectArray = $object->toArray();
            $objectArray['pagetitle'] = htmlentities($objectArray['pagetitle'], ENT_COMPAT, $charset);
            $objectArray['pagetitle'] = $objectArray['pagetitle'] . ' (' . $objectArray['id'] . ')' . ' - ' . $this->contextNames[$objectArray['context_key']];

            return $objectArray;
        }
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
        if (empty($sortKey)) $sortKey = $this->getProperty('sort');
        $c->sortby($sortKey,$this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit,$start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);

        /* Check on update if the value is returned on paged results. Else add it to result for displaying correct value. */
        $conditions = $this->getProperties();
        if (isset($conditions['value']) && !empty($conditions['value'])) {
            if (!empty($data['results'])) {
                $alreadyExists = false;
                foreach ($data['results'] as $result) {
                    if ($result->get('id') === $conditions['value']) {
                        $alreadyExists = true;
                    }
                }
            }
        }

        if (!$alreadyExists) {
            $data['results'][] = $this->modx->getObject('modResource', $conditions['value']);
        }

        return $data;
    }
}

return 'BenderamodResourceGetListProcessor';

<?php
/**
 * The base Bendera snippet.
 *
 * @package bendera
 */
$Bendera = $modx->getService(
    'bendera',
    'Bendera',
    $modx->getOption('bendera.core_path', null, $modx->getOption('core_path').'components/bendera/').'model/bendera/',
    $scriptProperties
);
if (!($Bendera instanceof Bendera)) {
    return '';
}

$tpl           = $modx->getOption('tpl', $scriptProperties, 'benderaItem');
$wrapper       = $modx->getOption('wrapperTpl', $scriptProperties, 'benderaWrapper');
$limit         = $modx->getOption('limit', $scriptProperties, 1);
$sortBy        = $modx->getOption('sortBy', $scriptProperties, 'startdate');
$sortDir       = $modx->getOption('sortDir', $scriptProperties, 'ASC');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$types         = $modx->getOption('types', $scriptProperties, null);
$contexts      = $modx->getOption('contexts', $scriptProperties, null);

$template = ($modx->resource) ? $modx->resource->get('template') : null;
$resource = ($modx->resource) ? $modx->resource->get('id') : null;
$date     = date('Y-m-d H:i:s');

$c = $modx->newQuery('BenderaItem');
$c->select('id,title,description,content,size,startdate,enddate,type,resource,categories,createdon,context,url');
$c->sortby($sortBy, $sortDir);

$where = array(
    'startdate:<=' => $date,
    'enddate:>='   => $date
);

if ($contexts) {
    $where[] = array('context:IN' => explode(',', $contexts));
}

if ($types) {
    $where[] = array('type:IN' => explode(',', $types));
}

$c->where($where);
$c->limit($limit);
$c->prepare();
$query = $c->toSQL();

$filter  = "AND (`categories` = '' OR FIND_IN_SET(" . $template . ", `categories`) > 0)";
$filter .= "AND (`resource` = '' OR FIND_IN_SET(" . $resource . ", `resource`) > 0)";
$filter .= " ORDER BY";

$query   = str_replace('ORDER BY', $filter, $query);
$results = $modx->query($query);
$rows    = '';
while ($item = $results->fetch(PDO::FETCH_ASSOC)) {
    $rows .= $modx->getChunk($tpl, $item);
}

if (!empty($wrapper)) {
    $output = $modx->getChunk($wrapper, array('output' => $rows));
} else {
    $output = $rows;
}

if (!empty($toPlaceholder)) {
    /* if using a placeholder, output nothing and set output to specified placeholder */
    $modx->setPlaceholder($toPlaceholder, $output);
    return '';
}

/* by default just return output */
return $output;
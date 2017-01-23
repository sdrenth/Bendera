<?php
/**
 * The base Bendera snippet.
 *
 * @package bendera
 */
$Bendera = $modx->getService('bendera','Bendera',$modx->getOption('bendera.core_path',null,$modx->getOption('core_path').'components/bendera/').'model/bendera/',$scriptProperties);
if (!($Bendera instanceof Bendera)) return '';

$c = $modx->newQuery('BenderaItem');
//$c->sortby($sortBy,$sortDir);
$c->where(array(
	'startdate:>' => time(),
	'enddate:<' => time(),
));
$c->limit($limit);
$items = $modx->getCollection('BenderaItem',$c);
$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[] = $Bendera->getChunk($tpl,$itemArray);
}

/* output */
$output = implode($outputSeparator,$list);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    /* if using a placeholder, output nothing and set output to specified placeholder */
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
/* by default just return output */
return $output;
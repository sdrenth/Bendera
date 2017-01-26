<?php
   //ajaxify our response data
function setResponse($data)
{
    $res = array(
        "success" => true,
        "rows" => $data
    );

    $d = json_encode($res);
    header("Content-type: text/html; charset=UTF-8");

    header("Content-Size: " . strlen($d));
    echo $d;
}

$c = $this->modx->newQuery('modResource');
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
    $c->where(
        array(
            'pagetitle:LIKE' => $_REQUEST['query'].'%',
        )
    );
}

$c->where(array(
   'context_key' => $_REQUEST['context'],
   'published' => 1,
   'deleted' => 0
));
$c->sortby('menuindex', 'ASC');
$resources = $this->modx->getCollection('modResource', $c);

foreach ($resources as $resource) {
    $values[] = array(
        'id' => $resource->get('id'),
        'pagetitle' => $resource->get('pagetitle').' ('.$resource->get('id').')'
    );
}

setResponse($values);
?>
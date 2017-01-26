<?php
/* Ajaxify our response data */
function setResponse($data)
{
    $res = array(
        "success" =>true,
        "rows" =>$data
    );

    $d = json_encode($res);
    header("Content-type: text/html; charset=UTF-8");
    header("Content-Size: " . strlen($d));

    echo $d;
}

$c = $this->modx->newQuery('modTemplate');
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
    $c->where(
        array(
            'templatename:LIKE' => $_REQUEST['query'].'%',
        )
    );
}

$c->where(
    array(
        'category'  => 0,
    )
);
$c->sortby('createdon', 'ASC');
$templates = $this->modx->getCollection('modTemplate', $c);

if ($templates) {
    foreach ($templates as $template) {
        $values[] = array(
            'id' => $template->get('id'),
            'templatename' => $template->get('templatename').' ('.$template->get('id').')'
        );
    }
}

setResponse($values);
?>
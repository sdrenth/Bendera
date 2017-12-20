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
 * Get a list of Items
 *
 * @package bendera
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start', $_REQUEST, 0);
$limit = $modx->getOption('limit', $_REQUEST, 20);

$c = $modx->newQuery('BenderaItem');
$c->where(array('context' => $_REQUEST['context']));
$count = $modx->getCount('BenderaItem', $c);

if ($isLimit) {
    $c->limit($limit, $start);
}

$items = $modx->getCollection('BenderaItem', $c);
$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    switch ($itemArray['type']) {
        case 'image':
            $itemArray['image_newimage'] = $itemArray['image'];
            break;
    }

    $itemArray['image_newimage'] = $itemArray['image'];

    $itemArray['resource'] = str_replace('||', ',', $itemArray['resource']);
    $list[]= $itemArray;
}

return $this->outputArray($list, $count);

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
 * Update an Item
 *
 * @package bendera
 * @subpackage processors
 */
if (empty($scriptProperties['id'])) {
    return $modx->error->failure($modx->lexicon('bendera.item_err_ns'));
}

$item = $modx->getObject('BenderaItem', $scriptProperties['id']);
if (!$item) {
    return $modx->error->failure($modx->lexicon('bendera.item_err_nf'));
}

$scriptProperties['active'] = ($scriptProperties['active']) ? $scriptProperties['active'] : 0;

if (is_array($scriptProperties['categories'])) {
    $scriptProperties['categories'] = implode(',', $scriptProperties['categories']);
}

$item->fromArray($scriptProperties);
if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('bendera.item_err_save'));
}

/* output */
$itemArray = $item->toArray('', true);

return $modx->error->success('', $itemArray);

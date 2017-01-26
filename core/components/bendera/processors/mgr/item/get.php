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
 * Get an Item
 * 
 * @package bendera
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('bendera.item_err_ns'));
$item = $modx->getObject('BenderaItem',$scriptProperties['id']);
if (!$item) return $modx->error->failure($modx->lexicon('bendera.item_err_nf'));

/* output */
$itemArray = $item->toArray('', true);
switch ($itemArray['type']) {
    case 'HTML':
    case 'html':
    case 'affiliate':
    case 'Affiliate':
        $itemArray['html'] = $itemArray['content'];
        break;
    case 'Flash':
    case 'flash':
        $itemArray['flash_swf'] = $itemArray['content'];
        break;
    case 'image':
    case 'Image':
    case 'Afbeelding':
        $itemArray['image'] = $itemArray['content'];
        $itemArray['image_newimage'] = $itemArray['content'];
        break;
}

return $modx->error->success('', $itemArray);

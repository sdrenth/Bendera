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
 * Create an Item
 *
 * @package bendera
 * @subpackage processors
 */
$item = $modx->newObject('BenderaItem');
switch ($_POST['type']) {
    case 'HTML':
    case 'html':
    case 'affiliate':
    case 'Affiliate':
        $_POST['content'] = $_POST['html'];
        break;
    case 'Flash':
    case 'flash':
        $_POST['content'] = $_POST['flash_swf'];
        break;
    case 'image':
    case 'Image':
    case 'Afbeelding':
        $_POST['content'] = $_POST['image'];
        break;
}

$_POST['categories'] = implode(',', $_POST['categories']);
$_POST['createdon'] = time();

$item->fromArray($_POST);

if (!$item->save()) {
    return $modx->error->failure($modx->lexicon('bendera.item_err_save'));
}

return $modx->error->success('', $item);

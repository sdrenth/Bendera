<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package bendera
 * @subpackage build
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('bendera.core_path', null, $modx->getOption('core_path') . 'components/bendera/') . 'model/';
            $modx->addPackage('bendera', $modelPath, 'modx_');

            $manager = $modx->getManager();

            $manager->createObjectContainer('BenderaItem');

            break;
    }
}

return true;
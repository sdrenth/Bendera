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

return $Bendera->getBenderaItems($scriptProperties);

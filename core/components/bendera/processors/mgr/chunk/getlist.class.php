<?php
/**
 * Grabs a list of chunks.
 *
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 * @subpackage processors.element.chunk
 */
class BenderamodChunkGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modChunk';
    public $languageTopics = array('chunk', 'category');

    public $chunks;

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $chunkConfig = $this->modx->getOption('bendera.chunks_config');
        if (!empty($chunkConfig)) {
            $this->chunks = json_decode($chunkConfig, true);

            $chunkIds = array();
            if ($this->chunks) {
                foreach ($this->chunks as $chunk) {
                    $chunkIds[] = $chunk['id'];
                }
            }

            $where['id:IN'] = $chunkIds;
        }

        $c->where($where);

        return $c;
    }

    public function prepareRow($object)
    {
        if ($object) {
            $objectArray = $object->toArray();

            if (!empty($this->chunks)) {
                foreach ($this->chunks as $chunk) {
                    if ((int) $chunk['id'] === $objectArray['id']) {
                        $objectArray['name'] = $chunk['name'];
                    }
                }
            }

            return $objectArray;
        }
    }
}

return 'BenderamodChunkGetListProcessor';

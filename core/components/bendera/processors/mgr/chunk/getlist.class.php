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

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $chunkConfig = $this->modx->getOption('bendera.chunks_config');
        if (!empty($chunkConfig)) {
            $chunks = json_decode($chunkConfig, true);

            $chunkIds = array();
            if ($chunks) {
                foreach ($chunks as $chunk) {
                    $chunkIds[] = $chunk['id'];
                }
            }

            $where['id:IN'] = $chunkIds;
        }

        $c->where($where);

        return $c;
    }
}

return 'BenderamodChunkGetListProcessor';

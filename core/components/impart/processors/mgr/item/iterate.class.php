<?php
/**
 * Send an Queue
 */
class impArtItemIterateProcessor extends modProcessor {
    public $objectType = 'impArticle';
    public $classKey = 'impArticle';

    public function process() {
        $c = $this->modx->newQuery($this->classKey, array(
            'imported' => 0,
            'alias_dublicate' => 0
        ));
        $c->limit(100);
        $c->sortby('id', 'ASC');

        $articles = $this->modx->getCollection($this->classKey, $c);
        foreach ($articles as $article) {
            $data = $article->toArray();

            $prefix = $this->modx->config['table_prefix'];
            $parent = $data['parent'];

            $query = $this->modx->query("SELECT MAX(menuindex) FROM {$prefix}site_content WHERE parent = {$parent}");
            $data['menuindex'] = intval($this->modx->getValue($query)) + 1;

            $response = $this->modx->runProcessor('resource/create', $data);
            if ($response->isError()) {
                return $response->response;
            }
            $article->set('imported', true);
            $article->save();
        }
        return $this->success();
    }

}

return 'impArtItemIterateProcessor';
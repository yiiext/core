<?php
/**
 * Предложения по дополнению к поведению TaggableBehaviour.
 */
include_once dirname(__FILE__) . '/CTaggableBehaviour.php';

class CTaggableBehaviourA extends CTaggableBehaviour {

    /**
     * Перегрузил метод, добавив третий параметр $with.
     */
    function findAllByTags($tags, CDbCriteria $criteria = null, $with = ''){
        $tags = self::getTagsArrayFromString($tags);
        if(empty($tags)) return array();
        $find = $this->owner;
        if (empty($with) === FALSE) $find = $find->with($with);
        return $find->findAll($this->getFindByTagsCriteria($tags, $criteria));
    }

    /**
     * Возвращает массив тегов и количество объектов с данным тегом.
     *
     * @return array
     */
    public function getTagsCounts() {
        if(!$this->cache || !($tags = $this->cache->get('Taggable'.$this->owner->tableName().'All'))){
            // getting associated tags
            $conn = $this->owner->dbConnection;
            $tags = $conn->createCommand(
                sprintf(
                    "SELECT t.name as name, count(*) as `count`
                    FROM `%s` t
                    JOIN `%s` et ON t.id = et.tagId
                    GROUP BY t.id",
                    $this->tagTable,
                    $this->getTagBindingTableName()
                )
            )->queryAll();

            if($this->cache) $this->cache->set('Taggable'.$this->owner->tableName().'All', $tags);
        }

        return $tags;
    }

    /**
     * Возвращает строку тегов. Каждый тег в виде ссылки.
     *
     * @param string
     * @return string
     */
    public function getTagsLinks($url) {
        $tags = $this->getTagsArray();
        foreach ($tags as $i => $tag) {
            $tags[$i] = CHtml::link($tag, array($url, 'tag' => $tag));
        }
        
        return implode(', ', $tags);
    }
}

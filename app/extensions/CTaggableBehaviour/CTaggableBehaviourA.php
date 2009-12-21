<?php
/**
 * Предложения по дополнению к поведению TaggableBehaviour.
 */
include_once dirname(__FILE__) . '/CTaggableBehaviour.php';

class CTaggableBehaviourA extends CTaggableBehaviour {
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

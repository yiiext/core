<?php
/**
 * TaggableBehaviour
 *
 * Provides tagging ability for a model.
 *
 * @version 0.7
 * @author Alexander Makarov
 * @link http://yiiframework.ru/forum/viewtopic.php?f=9&t=389
 */
class CTaggableBehaviour extends CActiveRecordBehavior {
    /**
     * Tags table name
     */
    public $tagTable = 'Tag';

    /**
     * Tag to Model binding table name.
     * Defaults to `{model table name}Tag`.
     */
    public $tagBindingTable = null;

    /**
     * Binding table model FK name.
     * Defaults to `{model table name with first lowercased letter}Id`.  
     */
    public $modelTableFk = null;

    /**
     * Create tags automatically or throw exception if tag does not exist
     */
    public $createTagsAutomatically = true;

    /**
     * Caching component Id
     */
    public $cacheID = false;

    private $tags = array();

    /**
     * @var CCache
     */
    private $cache = null;

    /**
     * @var array
     */
    private $taggedWith = array();

    /**
     * @return CDbConnection
     */
    protected function getConnection(){
        return $this->owner->dbConnection;
    }

    function init(){
        if($this->cacheID!==false){
            $this->cache = Yii::app()->getComponent($this->cacheID);
        }
    }

    function __toString(){
        return implode(', ', $this->tags);
    }

    /**
     * Get tag binding table name
     *
     * @access private
     * @return string
     */
    private function getTagBindingTableName(){
        if($this->tagBindingTable === null){
            $this->tagBindingTable = $this->owner->tableName().'Tag';
        }
        return $this->tagBindingTable;
    }

    /**
     * Get model table FK name
     *
     * @access private
     * @return string
     */
    private function getModelTableFkName(){
        if($this->modelTableFk === null){
            $tableName = $this->owner->tableName();
            $tableName[0] = strtolower($tableName[0]);
            $this->modelTableFk = $tableName.'Id';
        }
        return $this->modelTableFk;
    }

    /**
     * Set one or more tags
     *
     * @param string|array $tags
     * @return void
     */
    function setTags($tags){
        $tags = $this->toTagsArray($tags);
        $this->tags = array_unique($tags);

        return $this->owner;
    }

    /**
     * Add one or more tags
     *
     * @param string|array $tags
     * @return void
     */
    function addTags($tags){
        $tags = $this->toTagsArray($tags);
        $this->tags = array_unique(array_merge($this->tags, $tags));

        return $this->owner;
    }

    /**
     * Alias of addTags()
     *
     * @param string|array $tags
     * @return void
     */
    function addTag($tags){
        return $this->addTags($tags);        
    }

    /**
     * Remove one or more tags
     * 
     * @param string|array $tags
     * @return void
     */
    function removeTags($tags){
        $tags = $this->toTagsArray($tags);
        $this->tags = array_diff($this->tags, $tags);

        return $this->owner;
    }

    /**
     * Alias of removeTags
     *
     * @param  $tags
     * @return void
     */
    function removeTag($tags){
        return $this->removeTags($tags);
    }

    /**
     * Remove all tags
     *
     * @return void
     */
    function removeAllTags(){
        $this->tags = array();

        return $this->owner;
    }

    /**
     * Get tags
     *
     * @return array
     */
    function getTags(){
        return $this->tags;
    }

    /**
     * Get tags array from comma separated tags string 
     *
     * @access private
     * @param string|array $tags
     * @return array
     */
    protected function toTagsArray($tags){
        if(is_string($tags)) $tags = explode(',', $tags);
        array_walk($tags, array($this, 'trim'));
        return $tags;
    }

    /**
     * Used as a callback to trim tags
     *
     * @access private
     * @param  $item
     * @param  $key
     * @return string
     */
    private function trim(&$item, $key){
        $item = trim($item);            
    }

    /**
	 * Saves model tags on model save.
     * 
	 * @param CModelEvent $event
     * @throw Exception
	 */
    function afterSave($event){
        $conn = $this->getConnection();

        if(!$this->createTagsAutomatically){
            // checking if all of the tags are existing ones
            foreach($this->tags as $tag){
                $tagId = $conn->createCommand(
                    sprintf(
                        "SELECT id
                         FROM `%s`
                         WHERE name = %s",
                         $this->tagTable,
                         $conn->quoteValue($tag)
                    )
                )->queryScalar();

                if(!$tagId){
                    throw new Exception("$tag does not exist. Please add it before assigning or enable createTagsAutomatically.");
                }
            }
        }

        // delete all present tag bindings
        $conn->createCommand(
            sprintf(
                "DELETE
                 FROM `%s`
                 WHERE %s = %d",
                 $this->getTagBindingTableName(),
                 $this->getModelTableFkName(),
                 $this->owner->primaryKey
            )
        )->execute();

        // add new tag bindings and tags if there are any
        if(!empty($this->tags)){
            foreach($this->tags as $tag){
                // try to get existing tag
                $tagId = $conn->createCommand(
                    sprintf(
                        "SELECT id
                         FROM `%s`
                         WHERE name = %s",
                         $this->tagTable,
                         $conn->quoteValue($tag)
                    )
                )->queryScalar();

                // if there is no existing tag, create one
                if(!$tagId){
                    $conn->createCommand(
                        sprintf(
                            "INSERT
                             INTO `%s`(name)
                             VALUES (%s)",
                             $this->tagTable,
                             $conn->quoteValue($tag)
                        )
                    )->execute();

                    $tagId = $conn->getLastInsertID();
                }

                // bind tag to it's model
                $conn->createCommand(
                    sprintf(
                        "INSERT
                         INTO `%s`(%s, tagId)
                         VALUES (%d, %d)",
                         $this->getTagBindingTableName(),
                         $this->getModelTableFkName(),
                         $this->owner->primaryKey,
                         $tagId
                    )
                )->execute();
            }
        }
        
        if($this->cache) $this->cache->set($this->getCacheKey(), $this->tags);

        parent::afterSave($event);
    }

    /**
     * Deletes tag bindings on model delete. 
     *
     * @param CModelEvent $event
     */
    function afterDelete($event){
        // delete all present tag bindings
        $conn = $this->getConnection();
        $conn->createCommand(
            sprintf(
                "DELETE
                 FROM `%s`
                 WHERE %s = %d",
                 $this->getTagBindingTableName(),
                 $this->getModelTableFkName(),
                 $this->owner->primaryKey
            )
        )->execute();

        if($this->cache) $this->cache->delete($this->getCacheKey());

        parent::afterDelete($event);
    }

    /**
     * Fills model tags after finding model
     *
     * @param CModelEvent $event
     */
    function afterFind($event){
        if(!$this->cache || !($tags = $this->cache->get($this->getCacheKey()))){
            // getting associated tags
            $conn = $this->getConnection();
            $tags = $conn->createCommand(
                sprintf(
                    "SELECT t.name as name
                    FROM `%s` t
                    JOIN `%s` et ON t.id = et.tagId
                    WHERE et.%s = %d",
                    $this->tagTable,
                    $this->getTagBindingTableName(),
                    $this->getModelTableFkName(),
                    $this->owner->primaryKey
                )
            )->queryColumn();

            if($this->cache) $this->cache->set($this->getCacheKey(), $tags);
        }

        $this->tags = $tags;

        parent::afterFind($event);
    }

    /**
     * Returns key for caching model tags
     *
     * @access private
     * @return string
     */
    private function getCacheKey(){
        return 'Taggable'.$this->owner->tableName().$this->owner->primaryKey;
    }

    /**
     * Get criteria to find by tags
     *
     * @access private
     * @param $tags
     * @return CDbCriteria
     */
    protected function getFindByTagsCriteria($tags, CDbCriteria $criteria = null){
        if($criteria===null) $criteria = new CDbCriteria();

        $pk = $this->owner->tableSchema->primaryKey;

        if(!empty($tags)){
            $conn = $this->getConnection();
            for($i=0, $count=count($tags); $i<$count; $i++){
                $tag = $conn->quoteValue($tags[$i]);
                $criteria->join.=
                    "JOIN {$this->getTagBindingTableName()} bt$i ON {$this->owner->tableName()}.{$pk} = bt$i.{$this->getModelTableFkName()}
                     JOIN {$this->tagTable} tag$i ON tag$i.id = bt$i.tagId AND tag$i.`name` = $tag";
            }
        }

        return $criteria;
    }

    public function getAllTags($criteria = null){
        if(!$this->cache || !($tags = $this->cache->get('Taggable'.$this->owner->tableName().'All'))){
            // getting associated tags
            $builder = $this->owner->getCommandBuilder();
            $criteria = new CDbCriteria();
            $criteria->select = 'name';
            $tags = $builder->createFindCommand($this->tagTable, $criteria)->queryColumn();

            if($this->cache) $this->cache->set('Taggable'.$this->owner->tableName().'All', $tags);
        }

        return $tags;
    }

    /**
     * @param  $limit
     * @return array
     */
    public function getAllTagsWithModelsCount($criteria = null){
        if(!$this->cache || !($tags = $this->cache->get('Taggable'.$this->owner->tableName().'AllWithCount'))){
            // getting associated tags
            $conn = $this->getConnection();
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

            if($this->cache) $this->cache->set('Taggable'.$this->owner->tableName().'AllWithCount', $tags);
        }

        return $tags;
    }

    /**
     * Finds out if model has all tags specified.
     *
     * @param string|array $tags
     * @return boolean
     */    
    function hasTags($tags){
        $tags = $this->toTagsArray($tags);
        foreach($tags as $tag){
            if(!in_array($tag, $this->tags)) return false;
        }
        return true;
    }

    /**
     * Alias of hasTags() 
     *
     * @param string|array $tags
     * @return boolean     
     */
    function hasTag($tags){
        return $this->hasTags($tags);
    }

    function taggedWith($tags){
        $this->taggedWith = $this->toTagsArray($tags);

        return $this->owner;
    }

    function beforeFind(CEvent $event){
        if(!empty($this->taggedWith)){
            $criteria = $this->getFindByTagsCriteria($this->taggedWith);
            $this->owner->getDbCriteria()->mergeWith($criteria);
            $this->taggedWith = array();
        }
        
        parent::beforeFind($event);
    }
}

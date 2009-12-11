<?php
/**
 * CCart
 *
 * @version 0.1
 *
 * Allows to store information into the cart
 *
 * @TODO: can we use CModelBehaviour?
 */
class CCartBehaviour extends CActiveRecordBehavior {
    public $cartTable = 'Cart';

    public $cookieExpire = null;

    protected $userId = null;
    protected $userSid = null;

    protected $modelClass = null;
    protected $cookieName = null;

    /**
     * @var CDbConnection
     */
    protected $db;    

    function init(){
        $this->db = $this->owner->dbConnection;
        $this->modelClass = get_class($this->owner);
        $this->cookieName = $this->modelClass.'CartSid';

        if(!empty(Yii::app()->user) && !empty(Yii::app()->user->id)){
            $this->userId = Yii::app()->user->id;

            // if we have something in anonymous cart, move it to user's cart
            if(!empty($_COOKIE[$this->cookieName])){
                $this->moveAnonymousItemsToUser($_COOKIE[$this->cookieName]);
                // we don't need cookie anymore
                setcookie($this->cookieName, '', time() - 3600, '/');
            }
        }
        else {
            // anonymous cart
            if(!empty($_COOKIE[$this->cookieName])){
                $this->userSid = $_COOKIE[$this->cookieName];
            }
            else {
                // if user is anonymous and no userSid is set, generate it
                $this->userSid = md5(Yii::app()->request->getUserHostAddress().time());
            }
        }
    }

    /**
     * Move anonymous user cart to registered user
     *
     * @access private
     * @param string $userSid
     * @return void
     */
    private function moveAnonymousItemsToUser($userSid){
        return $this->db->createCommand(
            "UPDATE `%s`
             SET userId = %d
             WHERE userSid = '%s'
             AND model = '%s'",
             $this->cartTable,
             $this->userId,
             $userSid,
             $this->modelClass
        )->execute();
    }

    /**
     * Add model to cart
     *
     * @param int $id
     * @return void
     */
    function add($id = null){
        if($id = null) $id = $this->owner->primaryKey;
        if($this->userId){
            $this->db->createCommand(
                "REPLACE INTO `%s`(userId, id, model)
                 VALUES(%d, %d, '%s')",
                 $this->cartTable,
                 $this->userId,
                 $id,
                 $this->modelClass
            )->execute();
        }
        else {
            $this->db->createCommand(
                "REPLACE INTO `%s`(id, userSid, model)
                 VALUES(%d, '%s', '%s')",
                 $this->cartTable,
                 $id,
                 $this->userSid,
                 $this->modelClass
            )->execute();           

            // refresh cookie
            if($this->cookieExpire === null){
                setcookie($this->cookieName, $this->userSid, 0, '/');
            }
            else {
                setcookie($this->cookieName, $this->userSid, time()+$this->cookieExpire, '/');
            }
        }
    }

    /**
     * Remove item from cart
     *
     * @param int $id
     * @return void
     */
    function remove($id = null){
        if($id = null) $id = $this->owner->primaryKey;
        if($this->userId){
            $this->db->createCommand(
                "DELETE FROM `%s`
                 WHERE id = %d
                 AND userId = %d
                 AND model = '%s'",
                 $this->cartTable,
                 $id,
                 $this->userId,
                 $this->modelClass
            )->execute();
        }
        else {
            $this->db->createCommand(
                "DELETE FROM `%s`
                 WHERE id = %d
                 AND userId = 0
                 AND userSid = '%s'
                 AND model = '%s'",
                 $this->cartTable,
                 $this->userId,
                 $this->userSid,
                 $this->modelClass
            )->execute();
        }
    }

    /**
     * Get models count in user's cart
     *
     * @param  $criteria
     * @return CActiveRecord
     */
    function countAllInCart($criteria = null){
        $ids = $this->getCartIds();
        return $this->owner->count($this->getCartContentsCriteria($tags, $criteria));
    }

    /**
     * Get all models in user's cart
     *
     * @param  $criteria
     * @return CActiveRecord
     */
    function findAllInCart($criteria = null){
        $ids = $this->getCartIds();
        return $this->owner->findAll($this->getCartContentsCriteria($tags, $criteria));
    }

    /**
     * Get current user's cart models ids
     *
     * @access private
     * @return array
     */
    private function getCartIds(){
        if($this->userId){
            $command = $this->db->createCommand(
                "SELECT id
                 FROM `%s`
                 WHERE userId = %d
                 AND model = '%s'",
                 $this->cartTable,
                 $this->userId,
                 $this->modelClass
            );
        }
        else {
            $command = $this->db->createCommand(
                "SELECT nid
                 FROM `%s`
                 WHERE userSid = '%s'
                 AND model = '%s'",
                 $this->cartTable,
                 $this->userSid,
                 $this->modelClass
            );
        }

        return $command->queryColumn();
    }

    /**
     * Returns criteria for current user's cart contetns
     * @TODO: rewrite with JOIN instead of getCartIds()?
     *
     * @access private
     * @param CDbCriteria $criteria
     * @return CDbCriteria
     */
    private function getCartContentsCriteria(CDbCriteria $criteria = null){
        if($criteria===null) $criteria = new CDbCriteria();

        $criteria->where.="id IN (".implode(',', $this->getCartIds()).")";

        return $criteria;
    }
}
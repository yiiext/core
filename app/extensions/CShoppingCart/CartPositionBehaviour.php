<?php

/**
 * Объект позиции в корзине
 *
 * @author pirrat <mrakobesov@gmail.com>
 * @version 0.2 beta
 * @package ShoppingCart
 */

// We can use it for not AR models!
class CartPositionBehaviour extends CActiveRecordBehavior {

    /**
     * кол-во единиц позиции
     * @var int
     */
    private $quantity = 1;
    /**
     * Обновлять модель при востановлении из сессии?
     * @var boolean
     */
    private $refresh;


    public function init() {

    }

    /**
     *
     * @return float стоимость всех единиц позиции
     *
     */
    public function getSummPrice() {
        return $this->owner->getPrice() * $this->quantity;
    }

    /**
     *
     * @return int кол-во единиц данной позиции
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Установить количество этой позиции в заказе
     *
     * @param int quantity
     */
    public function setQuantity( $newVal ) {
        $this->quantity = $newVal;
    }

    /**
     * Магический метод, отрабатывает при востановлении
     * модели из сессии. Если установлен флаг, то обновляет данные модели
     */
    public function __wakeup() {
        if($this->refresh === true)
            $this->owner->refresh();
    }

    /**
     *
     * @param boolean $refresh
     */
    public function setRefresh($refresh) {
        $this->refresh = $refresh;
    }


    /*public function __sleep()
    {
	$this->owner->_md=null;
	return array_keys((array)$thi);
    }*/


}

<?php

/**
 * ICartPosition
 *
 * @author pirrat <mrakobesov@gmail.com>
 * @version 0.2 beta
 * @package ShoppingCart
 */
interface ICartPosition {
    /**
     * @return mixed уникальный индификатор
     */
    public function getId();
    /**
     * @return float цена
     */
    public function getPrice();
}
?>

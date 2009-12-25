<?php
class CShoppingCartTest extends CDbTestCase {
    public $fixtures=array(
        'books'=>'Book',
    );

    function testPut(){
        $this->setUp();

        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book);

        $this->assertEquals(1, $cart->getTotal());
    }

    /**
     * Remove item by object instead of id
     * @todo: is this what should be done?
     */
    function testPutZero(){
        $this->setUp();

        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book, 0);        
    }
    
    function testGet(){
        $cart = new CShoppingCart();
    }

    function testRemove(){
        $this->setUp();

        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book);
        $this->assertEquals(1, $cart->getTotal());

        $cart->remove("Book1");
        $this->assertEquals(0, $cart->getTotal());
    }
    
    function testGetTotal(){
        $this->setUp();
        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book, 3);

        $book = Book::model()->findByPk(2);
        $cart->put($book);

        $this->assertEquals(4, $cart->getTotal());
    }
    
    function testGetCost(){
        $this->setUp();
        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book);

        $book = Book::model()->findByPk(2);
        $cart->put($book, 2);
        
        $this->assertEquals(199.9, $cart->getCost());
    }
}

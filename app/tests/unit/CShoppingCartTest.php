<?php
class CShoppingCartTest extends CDbTestCase {
    public $fixtures=array(
        'books'=>'Book',
        'posts'=>'Post',
    );

    function testPut(){
        $this->setUp();

        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book);

        $this->assertEquals(1, $cart->getTotal());
    }

    function testGet(){
        $cart = new CShoppingCart();
        $book = $cart["Book1"];
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

    function testGetAll(){
        $this->setUp();
        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book);

        $book = Book::model()->findByPk(2);
        $cart->put($book, 2);        
        
        foreach($cart as $book){
            $this->assertTrue(in_array($book->id, array(1, 2)));
        }
    }

    function testArrayPut(){
        $this->setUp();
        $cart = new CShoppingCart();

        $cart[] = Book::model()->findByPk(1);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    function testWrongTypePut(){
        $this->setUp();
        $cart = new CShoppingCart();

        $cart->put(Post::model()->findByPk(1));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    function testWrongTypeArrayPut(){
        $this->setUp();
        $cart = new CShoppingCart();

        $cart[] = Post::model()->findByPk(1);
    }
}

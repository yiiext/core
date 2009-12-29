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
		$this->setUp();
        
		$cart = new CShoppingCart();
		
		$book = Book::model()->findByPk(1);
        $cart->put($book);
			
		$this->assertEquals(1, $cart["Book1"]->id);
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
		$this->assertEquals(0, $cart->getTotal());
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
	
	function testGetCount()
	{
		$this->setUp();
        $cart = new CShoppingCart();

        $book = Book::model()->findByPk(1);
        $cart->put($book, 3);

        $book = Book::model()->findByPk(2);
        $cart->put($book,6);

        $this->assertEquals(2, $cart->count());
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

        $cart["Book1"] = Book::model()->findByPk(1);
		$this->assertEquals(1, $cart->getTotal());
		$this->assertEquals(1, $cart["Book1"]->id);
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

        $cart[1] = Post::model()->findByPk(1);
    }
}

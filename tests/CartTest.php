<?php

/**
 * This file is part of Moltin Cart, a PHP package to handle
 * your shopping basket.
 *
 * Copyright (c) 2013 Moltin Ltd.
 * http://github.com/moltin/cart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   moltin/cart
 * @author    Chris Harvey <chris@molt.in>
 * @copyright 2013 Moltin Ltd.
 * @version   dev
 * @link      http://github.com/moltin/cart
 *
 */

use voku\Cart\Cart;
use voku\Cart\Storage\Runtime as RuntimeStore;
use voku\Cart\Identifier\Runtime as RuntimeIdentifier;

/**
 * Class CartTest
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Cart
   */
  public $cart;

  public function setUp()
  {
    $this->cart = new Cart(new RuntimeStore, new RuntimeIdentifier);
  }

  public function tearDown()
  {
    $this->cart->destroy();
  }

  public function testAlternateItemRemoval()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    $contents =& $this->cart->contents();

    self::assertNotEmpty($contents);

    foreach ($contents as $identifier => $item) {
      $this->cart->remove($identifier);
    }

    self::assertEmpty($contents);
  }

  public function testCartToArray()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    foreach ($this->cart->contents(true) as $item) {
      self::assertTrue(is_array($item));
    }

    foreach ($this->cart->contentsArray() as $item) {
      self::assertTrue(is_array($item));
    }
  }

  public function testFind()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    self::assertInstanceOf('\voku\Cart\Item', $this->cart->find('foo'));
  }

  public function testInsert()
  {
    $actualId = $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    $identifier = md5('foo' . serialize(array()));

    self::assertSame($identifier, $actualId);
  }


  public function testInsertIncrementsOverwriteId()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
        )
    );

    self::assertSame(150.0, $this->cart->total());

    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
        )
    );

    self::assertSame(300.0, $this->cart->total());

    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 2,
        )
    );

    self::assertSame(600.0, $this->cart->total());
  }

  public function testInsertIncrements()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150.0,
            'quantity' => 1,
        )
    );

    self::assertSame(150.0, $this->cart->total());

    $this->cart->insert(
        array(
            'id'       => 'foobar',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
        )
    );

    self::assertSame(300.0, $this->cart->total());

    $this->cart->insert(
        array(
            'id'       => 'foobar_new',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 2,
        )
    );

    self::assertSame(600.0, $this->cart->total());
  }

  public function testItemRemoval()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    $contents =& $this->cart->contents();

    self::assertNotEmpty($contents);

    foreach ($contents as $item) {
      $item->remove();
    }

    self::assertEmpty($contents);
  }

  public function testItemToArray()
  {
    $actualId = $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    self::assertTrue(is_array($this->cart->item($actualId)->toArray()));
  }

  public function testMagicUpdate()
  {
    $actualId = $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    foreach ($this->cart->contents() as $item) {
      $item->name = 'baz';
    }

    self::assertSame($this->cart->item($actualId)->name, 'baz');
  }

  public function testOptions()
  {
    $actualId = $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'options'  => array(
                'size' => 'L',
            ),
        )
    );

    $item = $this->cart->item($actualId);

    self::assertTrue($item->hasOptions());
    self::assertNotEmpty($item->options);

    $item->options = array();

    self::assertFalse($item->hasOptions());
    self::assertEmpty($item->options);
  }

  public function testTax()
  {
    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'tax'      => 20,
        )
    );

    // Test that the tax is being calculated successfully
    self::assertSame($this->cart->total(), 120.0);
    self::assertSame($this->cart->totalWithTax(), 120.0);

    // Test that the total method can also return the pre-tax price if false is passed
    self::assertSame($this->cart->total(false), 100.0);
    self::assertSame($this->cart->totalWithoutTax(), 100.0);
  }

  public function testTotalItems()
  {
    $adding = mt_rand(1, 200);
    $actualTotal = 0;

    for ($i = 1; $i <= $adding; $i++) {
      $quantity = mt_rand(1, 20);

      $this->cart->insert(
          array(
              'id'       => uniqid('foobar', true),
              'name'     => 'bar',
              'price'    => 100,
              'quantity' => $quantity,
          )
      );

      $actualTotal += $quantity;
    }

    self::assertSame($this->cart->totalItems(), $actualTotal);
    self::assertSame($this->cart->totalItems(true), $adding);
    self::assertSame($this->cart->totalUniqueItems(), $adding);
  }

  public function testTotals()
  {
    // Generate a random price and quantity
    $price = mt_rand(20, 99999);
    $quantity = mt_rand(1, 10);

    $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => $price,
            'quantity' => $quantity,
        )
    );

    // Test that the total is being calculated successfully
    self::assertSame($this->cart->total(), (float)$price * $quantity);
  }

  public function testUpdate()
  {
    $actualId = $this->cart->insert(
        array(
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
        )
    );

    $this->cart->update($actualId, 'name', 'baz');

    self::assertSame($this->cart->item($actualId)->name, 'baz');
  }
}

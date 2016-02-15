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

namespace voku\Cart\Storage;

use voku\Cart\Item;
use voku\Cart\StorageInterface;

/**
 * Shopping-Storage Runtime
 */
class Runtime implements StorageInterface
{

  /**
   * @var array
   */
  protected static $cart = array();

  /**
   * @var mixed
   */
  protected $id;

  /**
   * Retrieve the cart data
   *
   * @param bool $asArray
   *
   * @return array
   */
  public function &data($asArray = false)
  {
    $cart = &static::$cart[$this->id];

    if (!$asArray) {
      return $cart;
    }

    // init
    $data = array();

    foreach ($cart as &$item) {
      /* @var $item Item */
      $data[] = $item->toArray();
    }

    return $data;
  }

  /**
   * Destroy the cart
   *
   * @return void
   */
  public function destroy()
  {
    static::$cart[$this->id] = array();
  }

  /**
   * Returns the first occurance of an item with a given id
   *
   * @param  string $id The item id
   *
   * @return Item       Item object
   */
  public function find($id)
  {
    foreach (static::$cart[$this->id] as $item) {

      if ($item->id == $id) {
        return $item;
      }
    }

    return false;
  }

  /**
   * Return the current cart identifier
   *
   * @return mixed|void
   */
  public function getIdentifier()
  {
    return $this->id;
  }

  /**
   * Check if the item exists in the cart
   *
   * @param $identifier
   *
   * @return bool
   */
  public function has($identifier)
  {
    foreach (static::$cart[$this->id] as $item) {

      /* @var $item Item */
      if ($item->getIdentifier() == $identifier) {
        return true;
      }
    }

    return false;
  }

  /**
   * Add or update an item in the cart
   *
   * @param Item $item
   *
   * @return bool
   */
  public function insertUpdate(Item $item)
  {
    static::$cart[$this->id][$item->getIdentifier()] = $item;

    return true;
  }

  /**
   * Get a single cart item by id
   *
   * @param $identifier
   *
   * @return bool|Item The item class
   */
  public function item($identifier)
  {
    foreach (static::$cart[$this->id] as $item) {

      /* @var $item Item */
      if ($item->getIdentifier() === $identifier) {
        return $item;
      }
    }

    return false;
  }

  /**
   * Remove an item from the cart
   *
   * @param  mixed $id
   *
   * @return void
   */
  public function remove($id)
  {
    unset(static::$cart[$this->id][$id]);
  }

  /**
   * Set the cart identifier
   *
   * @param string $id identifier
   */
  public function setIdentifier($id)
  {
    $this->id = $id;

    if (!array_key_exists($this->id, static::$cart)) {
      static::$cart[$this->id] = array();
    }
  }

}
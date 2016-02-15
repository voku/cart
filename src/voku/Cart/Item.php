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

namespace voku\Cart;

/**
 * Class Item
 */
class Item
{
  /**
   * @var string
   */
  protected $identifier;

  /**
   * @var StorageInterface
   */
  protected $store;

  /**
   * @var Tax
   */
  protected $tax;

  /**
   * @var array
   */
  protected $data = array();

  /**
   * Construct the item
   *
   * @param string           $identifier
   * @param array            $item
   * @param StorageInterface $store
   */
  public function __construct($identifier, array $item, StorageInterface $store)
  {
    $this->identifier = $identifier;
    $this->store = $store;

    foreach ($item as $key => $value) {
      $this->data[$key] = $value;
    }

    $item['tax'] = isset($item['tax']) ? $item['tax'] : 0;

    $this->tax = new Tax($item['tax']);
  }

  /**
   * Return the value of protected methods
   *
   * @param  string $param The key to get
   *
   * @return mixed
   */
  public function __get($param)
  {
    if ($param == 'identifier') {
      return $this->identifier;
    }

    /** @noinspection PhpVariableVariableInspection */
    if (isset($this->$param) === true) {
      return $this->data[$param];
    } else {
      return null;
    }
  }

  /**
   * Check initialization of data array params using isset magic method
   *
   * INFO: This is primarily to allow Twig to access the properties of Item->data
   *       http://twig.sensiolabs.org/doc/recipes.html#using-dynamic-object-properties
   *
   * @param string $param The key to check
   *
   * @return bool
   */
  public function __isset($param)
  {
    if ($param == 'identifier') {
      return true;
    }

    return array_key_exists($param, $this->data);
  }

  /**
   * Update data array using set magic method
   *
   * @param string $param The key to set
   * @param mixed  $value The value to set $param to
   */
  public function __set($param, $value)
  {
    $this->data[$param] = $value;
  }

  /**
   * get the item-identifier
   *
   * @return string
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }

  /**
   * Check if this item has options
   *
   * @return boolean Yes or no?
   */
  public function hasOptions()
  {
    return array_key_exists('options', $this->data) && !empty($this->data['options']);
  }

  /**
   * Removes the current item from the cart
   *
   * @return void
   */
  public function remove()
  {
    $this->store->remove($this->identifier);
  }

  /**
   * Return the total tax for this item
   *
   * @param boolean $single Tax for single item or all?
   *
   * @return float
   */
  public function tax($single = false)
  {
    $quantity = $single ? 1 : $this->quantity;

    return $this->tax->rate($this->price * $quantity);
  }

  /**
   * Convert the item into an array
   *
   * @return array The item data
   */
  public function toArray()
  {
    return $this->data;
  }

  /**
   * Return the total of the item, with or without tax
   *
   * @param  boolean $includeTax Whether or not to include tax
   *
   * @return float              The total, as a float
   */
  public function total($includeTax = true)
  {
    $price = $this->price;

    if ($includeTax) {
      $price = $this->tax->add($price);
    }

    return (float)($price * $this->quantity);
  }

  /**
   * Update a single key for this item, or multiple
   *
   * @param array|string $key The array key to update, or an array of key-value pairs to update
   * @param mixed        $value
   */
  public function update($key, $value = null)
  {
    if (is_array($key)) {

      foreach ($key as $updateKey => $updateValue) {
        $this->update($updateKey, $updateValue);
      }

    } else {

      $key = (string)$key;

      //
      // update the "quantity"
      //

      if ($key == 'quantity') {

        // if no quantity, remove the item and "return" here
        if ($value <= 0) {
          $this->remove();

          return;
        }
      }

      //
      // update the "tax"
      //

      if ($key == 'tax' && is_numeric($value)) {
        $this->tax = new Tax($value);
      }

      //
      // update the item
      //

      $this->data[$key] = $value;
    }
  }

}
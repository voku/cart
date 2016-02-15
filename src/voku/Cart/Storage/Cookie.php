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
 * @package moltin/cart
 * @author Chris Harvey <chris@molt.in>
 * @copyright 2013 Moltin Ltd.
 * @version dev
 * @link http://github.com/moltin/cart
 *
 */

namespace voku\Cart\Storage;

/**
 * Shopping-Storage via Cookie.
 */
class Cookie extends Runtime
{

  /**
   * The cookie store constructor
   */
  public function restore()
  {
    if (isset($_COOKIE['cart'])) {
      parent::$cart = unserialize($_COOKIE['cart']);
    }
  }

  /**
   * The cookie destructor.
   */
  public function __destruct()
  {
  }

  /**
   * The cookie store function.
   */
  public function setCookie()
  {
    setcookie('cart', serialize(parent::$cart), time() + (86400 * 3), "/");
  }


}
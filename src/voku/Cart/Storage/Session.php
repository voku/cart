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

/**
 * Shopping-Storage via Session.
 */
class Session extends Runtime
{

  /**
   * The Session store constructor
   */
  public function restore()
  {
    if (isset($_SESSION['cart'])) {
      parent::$cart = unserialize($_SESSION['cart']);
    }
  }

  /**
   * Save cart to session.
   *
   * Do not call this from session storage destructor as the destructor of the cart might have already
   * been called before.
   */
  public function save()
  {
    $_SESSION['cart'] = serialize(parent::$cart);
  }

}
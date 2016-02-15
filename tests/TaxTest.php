<?php

/**
 * This file is part of Moltin Tax, a PHP package to calculate
 * tax rates.
 *
 * Copyright (c) 2013 Moltin Ltd.
 * http://github.com/moltin/tax
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   moltin/tax
 * @author    Chris Harvey <chris@molt.in>
 * @copyright 2013 Moltin Ltd.
 * @version   dev
 * @link      http://github.com/moltin/tax
 *
 */

use voku\Cart\Tax;

/**
 * Class TaxTest
 */
class TaxTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var Tax
   */
  public $tax;

  public function setUp()
  {
    $this->tax = new Tax(20);
  }

  public function testAdd()
  {
    self::assertEquals($this->tax->add(100), 120);
  }

  public function testDecuct()
  {
    self::assertEquals($this->tax->deduct(100), 80);
  }

  public function testModifiers()
  {
    self::assertEquals($this->tax->addModifier, 1.2);
    self::assertEquals($this->tax->deductModifier, 0.8);
  }

  public function testPercentageCalculation()
  {
    $tax = new Tax(100, 120);

    self::assertEquals($tax->rate(100), 20);
  }

  public function testRate()
  {
    self::assertEquals($this->tax->rate(100), 20);
  }
}
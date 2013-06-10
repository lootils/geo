<?php
/**
 * @file
 * Test the Yee equations.
 */

namespace Lootils\Geo;

require_once 'PHPUnit/Autoload.php';
require_once 'vendor/autoload.php';

use \Lootils\Geo\Location;

class YeeTest extends \PHPUnit_Framework_TestCase {

  public function testDistance() {

    $location1 = new Location(42.7186, -84.468466);    // Michigan State University
    $location2 = new Location(42.274919, -83.740672);  // University of Michigan

    $distance = $location1->distance($location2, 'yee');

    $this->assertTrue(is_float($distance));

    // Because these are very long numbers we round to mm.
    $this->assertEquals(77390.178, round($distance, 3));
  }

}
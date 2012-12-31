<?php
/**
 * @file
 * Test the Vincenty equations.
 */

namespace Lootils\Geo;

require_once 'PHPUnit/Autoload.php';
require_once 'vendor/autoload.php';

use \Lootils\Geo\Location;
use \Lootils\Geo\Geo;

class VincentyTest extends \PHPUnit_Framework_TestCase {

  public function testGoogle() {

    $google = new Location(37.422045, -122.084347);  // Google HQ
    $sf = new Location(37.77493, -122.419416);       // San Fransisco, CA
    $ef = new Location(48.8582, 2.294407);           // Eiffel Tower
    $opera = new Location(-33.856553, 151.214696);   // Sydney Opera House

    $this->assertEquals(49087.066, round($google->distance($sf, 'vincenty'), 3));
    $this->assertEquals(8989724.399, round($google->distance($ef, 'vincenty'), 3));
    $this->assertEquals(11939773.640, round($google->distance($opera, 'vincenty'), 3));
  }

}
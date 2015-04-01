<?php
/**
 * @file
 * Test the location calculations.
 */

namespace Lootils\Geo;

use \Lootils\Geo\Location;
use \Lootils\Geo\Exception;

class LocationTest extends \PHPUnit_Framework_TestCase {

  public function testCoords() {

    $location = new Location(42.7186, -84.468466);

    $this->assertEquals($location->latitude(), 42.7186);
    $this->assertEquals($location->longitude(), -84.468466);

  }

  public function testHeight() {
    $location = new Location(42.7186, -84.468466);
    $location->setHeight(300);

    $this->assertEquals($location->height(), 300);
  }

  public function testDMS() {

    $location = new Location(42.7186, -84.468466);

    $this->assertEquals("42 43 6.960000 N", $location->DMSLatitude());
    $this->assertEquals("-84 28 6.477600 W", $location->DMSLongitude());
  }

  public function testRanges() {

    $location = new Location(42.7186, -84.468466);

    $latRange = $location->latitudeRange(10000);
    $latmin = round($latRange['min'], 5);
    $latmax = round($latRange['max'], 5);

    $longRange = $location->longitudeRange(20000);
    $longmin = round($longRange['min'], 5);
    $longmax = round($longRange['max'], 5);

    $this->assertEquals(42.62863, $latmin);
    $this->assertEquals(42.80857, $latmax);
    $this->assertEquals(-84.71339, $longmin);
    $this->assertEquals(-84.22355, $longmax);
  }

  public function testExceptions() {

    $location = new Location(42.7186, -84.468466);
    $location2 = new Location(42.274919, -83.740672);

    try {
      $distance = $location->distance($location2, 'foo');

      $this->fail();
    }
    catch (Exception $e) {
      $this->assertEquals('Distance method not registered.', $e->getMessage());
    }

    $location->registerDistanceMethod('bar', '\Lootils\Geo\DoesNotExist');

    try {
      $distance = $location->distance($location2, 'bar');

      $this->fail();
    }
    catch (Exception $e) {
      $this->assertEquals('The class associated with the name does not exist.', $e->getMessage());
    }
  }

}

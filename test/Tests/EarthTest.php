<?php
/**
 * @file
 * Test the EarthInfo stuff.
 */

namespace Lootils\Geo;

use \Lootils\Geo\Earth;

class EarthTest extends \PHPUnit_Framework_TestCase {

  public function testToDMS() {

    // These are the coordinates for Michigan State University.
    $coordinate1 = 42.7186;
    $coordinate2 = -84.468466;

    $earthInfo = new Earth;

    $dms1 = $earthInfo->convertDecToDMS($coordinate1);
    $dms2 = $earthInfo->convertDecToDMS($coordinate2);

    $this->assertEquals($dms1, array('degrees' => 42, 'minutes' => 43, 'seconds' => 6.96));
    $this->assertEquals($dms2, array('degrees' => -84, 'minutes' => 28, 'seconds' => 6.4776));

  }

  public function testToDec() {

    // These are the coordinates for the University of Michigan.
    $lat = array(
      'degrees' => 42,
      'minutes' => 16,
      'seconds' => 29.7078,
    );
    $long = array(
      'degrees' => -83,
      'minutes' => 44,
      'seconds' => 26.4192,
    );

    $earthInfo = new Earth;

    $this->assertEquals(42.274919, round($earthInfo->convertDMStoDec($lat['degrees'], $lat['minutes'], $lat['seconds']), 6));
    $this->assertEquals(-83.740672, round($earthInfo->convertDMStoDec($long['degrees'], $long['minutes'], $long['seconds']), 6));
  }

  /**
   * Test the conversions between distance formats.
   */
  public function testDistanceConversions() {

    $earthInfo = new Earth;

    $meters = 50000;
    $nm = $earthInfo->convertMetersToNauticalMiles($meters);

    $this->assertEquals(26.99784, round($nm, 5));
    $this->assertEquals(50000, round($earthInfo->convertNauticalMilesToMeters($nm), 5));

  }

  public function testEarthEccentricitySq() {
    // arrange
    $expected = 0.0066943799901413165;
    $earthInfo = new Earth;

    // act
    $actual = $earthInfo->earthEccentricitySq();

    // assert
    $this->assertEquals($expected, $actual);
  }

}

<?php
/**
 * @file
 * Calculate the distance between two locations on the Earth and calculate ranges.
 *
 * The equations are from Ka-Ping Yee (http://zesty.ca/) and usage permission 
 * was given on December 28, 2012 - https://twitter.com/zestyping/status/284747131867254784
 */

namespace Lootils\Geo\Method;

use \Lootils\Geo\Earth;
use \Lootils\Geo\LocationInterface;
use \Lootils\Geo\Method\DistanceInterface;

class Yee extends Earth implements DistanceInterface {

  /**
   * Calculate the distance between two locations.
   * 
   * Note, this is an estimate. The further the two places are from each other
   * the less accurate the distance calculation will be. This method also assumes
   * all locations are at the equator.
   */
  public function distance(LocationInterface $location1, LocationInterface $location2) {
    $radLong1 = deg2rad($location1->longitude());
    $radLat1 = deg2rad($location1->latitude());
    $radLong2 = deg2rad($location2->longitude());
    $radLat2 = deg2rad($location2->latitude());


    $radius = $this->earthRadius(($location1->latitude() + $location2->latitude()) / 2);

    $cosangle = cos($radLat1)*cos($radLat2) *
      (cos($radLong1)*cos($radLong2) + sin($radLong1)*sin($radLong2)) +
      sin($radLat1)*sin($radLat2);

    return acos($cosangle) * $radius;
  }

}
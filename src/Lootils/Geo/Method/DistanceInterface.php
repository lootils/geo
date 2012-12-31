<?php

namespace Lootils\Geo\Method;

use \Lootils\Geo\LocationInterface;

interface DistanceInterface {
  /**
   * Calculate the distance between two locations.
   *
   * @return float
   *   The distance, in meters, between the two locations.
   */
  public function distance(LocationInterface $location1, LocationInterface $location2);
}
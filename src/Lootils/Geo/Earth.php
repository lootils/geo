<?php
/**
 * @file
 * Information about the shape and structure of Earth along with general Earth helper mathods.
 */

namespace Lootils\Geo;

/**
 * Information about the shape of the Earth.
 */
class Earth {

  /**
   * The distance is in meters.
   */
  public function earthRadiusSemimajor() {
    return 6378137.0;
  }

  public function earthEccentricitySq() {
    return 2 * $this->earthFlattening() - pow($this->earthFlattening(), 2);
  }

  public function earthFlattening() {
    return 1 / 298.257223563;
  }

  public function earthRadiusSemiminor() {
    return ($this->earthRadiusSemimajor() * (1 - $this->earthFlattening()));
  }

  /**
   * Get the radius of the Earth at a specific latitude.
   * 
   * @param float $latitude
   *   The latitude at which to get the radius of the Earth.
   *
   * @return float
   *   The radius of the Earth at this latitude in meters.
   */
  public function earthRadius($latitude) {
    $radLat = deg2rad($latitude);

    $x = cos($radLat) / $this->earthRadiusSemimajor();
    $y = sin($radLat) / $this->earthRadiusSemiminor();

    return 1 / (sqrt($x * $x + $y * $y));
  }

  /**
   * Convert a coordinate into Degree Minute Second format.
   *
   * @param float $coordinate
   *   The coordinate in decimal format to convert.
   *
   * @return array
   *   An array with the keys degrees, minutes, and seconds
   */
  public function convertDecToDMS($coordinate) {

    $dms = array();
    $parts = explode('.', $coordinate);

    // The degrees portion.
    $dms['degrees'] = $parts[0];

    // Calculate the minutes
    $temp = ("0." . $parts[1]) * 3600;
    $dms['minutes'] = floor($temp / 60);

    // Find the seconds left over
    $dms['seconds'] = $temp - ($dms['minutes'] * 60);

    return $dms;
  }

  /**
   * Convert Degrees Minutes Seconds to Decimal for a coordinate.
   *
   * @param int $degrees
   * @param int $minutes
   * @param float $seconds
   *
   * @return float
   *   The coordinate in decimal format.
   */
  public function convertDMStoDec($degrees, $minutes, $seconds) {
    if ($degrees < 0) {
      return $degrees - ((($minutes * 60) + $seconds) / 3600);
    }
    
    return $degrees + ((($minutes * 60) + $seconds) / 3600);
  }

  /**
   * Convert meters to nautical miles.
   *
   * @see https://en.wikipedia.org/wiki/Nautical_mile
   * 
   * @param float $meters
   *   A distance in meters.
   *
   * @return float
   *   The same distance in nautical miles.
   */
  public function convertMetersToNauticalMiles($meters) {
    return $meters / 1852;
  }

  /**
   * Convert nautical miles to meters.
   *
   * @param float $nm
   *   A distance in nautical miles.
   *
   * @return float
   *   The same distance in meters.
   */
  public function convertNauticalMilesToMeters($nm) {
    return $nm * 1852;
  }

}
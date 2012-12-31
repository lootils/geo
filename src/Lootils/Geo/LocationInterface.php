<?php
/**
 * @file
 * Define the interface for a location to be used in the equations.
 */

namespace Lootils\Geo;

/**
 * Objects of type Location represent a coordinate location on the Earth.
 */
interface LocationInterface {

  /**
   * Create a new position object for a location on the Earth.
   *
   * @param float $latitude
   *   The latitude for this location in degrees (optional).
   * @param float $longitude
   *   The longitude for this location in degrees (optional).
   */
  public function __construct($latitude = NULL, $longitude = NULL);

  /**
   * Set the latitude for the location.
   *
   * @param float $latitude
   *   The latitude for the location.
   *
   * @return mixed
   *   The current object ($this) so it can be used with chaining.
   */
  public function setLatitude($latitude);

  /**
   * Get the current latitude.
   * 
   * @return float
   *   The latitude for the location.
   */
  public function latitude();

  /**
   * Get the current latitude in Degrees, Minutes, Seconds notation.
   *
   * @param string $format
   *   The format string to use for the response. Defaults to %d %d %F %s
   * 
   * @return string
   *   A string with the current latitude in DMS format.
   */
  public function DMSLatitude($format = '%d %d %F %s');

  /**
   * Set the longitude for the location.
   *
   * @param float $longitude
   *   The longitude for the location.
   *
   * @return mixed
   *   The current object ($this) so it can be used with chaining.
   */
  public function setLongitude($longitude);

  /**
   * Get the current longitude.
   *
   * @return float
   *   The longitude for the location.
   */
  public function longitude();

  /**
   * Get the current longitude in Degrees, Minutes, Seconds notation.
   *
   * @param string $format
   *   The format string to use for the response. Defaults to %d %d %F %s
   * 
   * @return string
   *   A string with the current longitude in DMS format.
   */
  public function DMSLongitude($format = '%d %d %F %s');

  /**
   * Set the height for the location.
   *
   * @param float $height
   *   The height for the location relative to sea level.
   *
   * @return mixed
   *   The current object ($this) so it can be used with chaining.
   */
  public function setHeight($height);

  /**
   * Get the current height relative to sea level.
   *
   * @return float
   *   The current height relative to sea level.
   */
  public function height();

  /**
   * Find a range of latitudes given a center point.
   *
   * This is useful when you have a center point and want to find a range to
   * query against a set. For example, you want to find all the businesses to
   * display on the glowing rectangle (mobile device) given a center point and
   * radius.
   *
   * @param float $distance
   *   The distance in meters from the center point to calculate.
   *
   * @return array
   *   An array with the two bounding values (floats) for the range.
   */
  public function latitudeRange($distance);

  /**
   * Find a range of longitude given a center point.
   *
   * This is useful when you have a center point and want to find a range to
   * query against a set. For example, you want to find all the businesses to
   * display on the glowing rectangle (mobile device) given a center point and
   * radius.
   *
   * @param float $distance
   *   The distance in meters from the center point to calculate.
   *
   * @return array
   *   An array with the two bounding values (floats) for the range.
   */
  public function longitudeRange($distance);

  /**
   * Register a distance method to use for calculations.
   * @param string $name
   *   The name of the method to use.
   * @param string $klass
   *   The name of the class to instanciate when performing the distance
   *   calculation for the named method.
   *
   * @return mixed
   *   The current object ($this) so this can be used in chaining.
   */
  public function registerDistanceMethod($name, $klass);

  /**
   * Remove all the currently reginstered distance methods.
   *
   * @return mixed
   *   The current object ($this) so this method can be used in chaining.
   */
  public function removeDistanceMethods();

  /**
   * Get all the distance methods currently available.
   *
   * @return array
   *   All the distance methods currently registered.
   */
  public function distanceMethods();

  /**
   * Get the distance between this location and another one.
   *
   * @param \Lootils\Geo\LocationInterface $location
   *   The other location object to diff the distance of.
   * @param string $method
   *   The registered method to use for this calculation. Because the Earth is
   *   an irregular ellipse there are multiple math equations attempting to
   *   best calculate the distance.
   *
   * @return float
   *   The estimated distance between the two locations in meters.
   */
  public function distance(LocationInterface $location, $method = 'default');

}
<?php
/**
 * @file
 * A Location on the Earth used for calculations.
 *
 * The equations are from Ka-Ping Yee (http://zesty.ca/) and usage permission 
 * was given on December 28, 2012 - https://twitter.com/zestyping/status/284747131867254784
 */

namespace Lootils\Geo;

use \Lootils\Geo\Earth;
use \Lootils\Geo\LocationInterface;
use \Lootils\Geo\Method\DistanceInterface;
use \Lootils\Geo\Exception;

/**
 * Objects of type Location represent a coordinate location on the Earth.
 */
class Location extends Earth implements LocationInterface {

  /**
   * The latitude for this location (in degrees).
   */
  protected $latitude = NULL;

  /**
   * The longitude for this location (in degrees).
   */
  protected $longitude = NULL;

  /**
   * The height relative to sea level.
   */
  protected $height = 0;

  /**
   * The distance methods.
   */
  protected $distanceMethods = array();
  protected $distanceCache = array();

  /**
   * Create a new position object for a location on the Earth.
   *
   * @param float $latitude
   *   The latitude for this location in degrees (optional).
   * @param float $longitude
   *   The longitude for this location in degrees (optional).
   */
  public function __construct($latitude = NULL, $longitude = NULL) {

    if (!is_null($latitude)) {
      $this->setLatitude($latitude);
    }
    if (!is_null($longitude)) {
      $this->setLongitude($longitude);
    }

    $this->registerDistanceMethod('default', '\Lootils\Geo\Method\Vincenty');
    $this->registerDistanceMethod('yee', '\Lootils\Geo\Method\Yee');
    $this->registerDistanceMethod('vincenty', '\Lootils\Geo\Method\Vincenty');
  }

  /**
   * Set the latitude for the location.
   *
   * @param float $latitude
   *   The latitude for the location.
   *
   * @return \Lootils\Geo\Location
   *   The current object so it can be used with chaining.
   */
  public function setLatitude($latitude) {
    $this->latitude = $latitude;

    return $this;
  }

  /**
   * Get the current latitude.
   * 
   * @return float
   *   The latitude for the location.
   */
  public function latitude() {
    return $this->latitude;
  }

  /**
   * Get the current latitude in Degrees, Minutes, Seconds notation.
   *
   * @param string $format
   *   The format string to use for the response. Defaults to %d %d %F %s
   * 
   * @return string
   *   A string with the current latitude in DMS format.
   */
  public function DMSLatitude($format = '%d %d %F %s') {

    $lat = $this->convertDecToDMS($this->latitude());

    $direction = 'N';
    if ($lat['degrees'] < 0) {
      $direction = 'S';
    }

    return sprintf($format, $lat['degrees'], $lat['minutes'], $lat['seconds'], $direction);
  }

  /**
   * Set the longitude for the location.
   *
   * @param float $longitude
   *   The longitude for the location.
   *
   * @return \Lootils\Geo\Location
   *   The current object so it can be used with chaining.
   */
  public function setLongitude($longitude) {
    $this->longitude = $longitude;

    return $this;
  }

  /**
   * Get the current longitude.
   *
   * @return float
   *   The longitude for the location.
   */
  public function longitude() {
    return $this->longitude;
  }

  /**
   * Get the current longitude in Degrees, Minutes, Seconds notation.
   *
   * @param string $format
   *   The format string to use for the response. Defaults to %d %d %F %s
   * 
   * @return string
   *   A string with the current longitude in DMS format.
   */
  public function DMSLongitude($format = '%d %d %F %s') {
    $long = $this->convertDecToDMS($this->longitude());

    $direction = 'E';
    if ($long['degrees'] < 0) {
      $direction = 'W';
    }

    return sprintf($format, $long['degrees'], $long['minutes'], $long['seconds'], $direction);
  }

  /**
   * Set the height for the location.
   *
   * @param float $height
   *   The height for the location relative to sea level.
   *
   * @return \Lootils\Geo\Location
   *   The current object so it can be used with chaining.
   */
  public function setHeight($height) {
    $this->height = $height;

    return $this;
  }

  /**
   * Get the current height relative to sea level.
   *
   * @return float
   *   The current height relative to sea level.
   */
  public function height() {
    return $this->height;
  }

  /**
   * Get the Cartesian coordinates (x, y, z) for this location.
   *
   * @return array
   *   An array of floats keyed with x, y, z for the location.
   */
  public function cartesian() {

    // Convert from degrees to radians.
    $radLong = deg2rad($this->longitude());
    $radLat = deg2rad($this->latitude());

    $coslong = cos($radLong);
    $coslat = cos($radLat);
    $sinlong = sin($radLong);
    $sinlat = sin($radLat);

    $radius = $this->earthRadiusSemimajor() / sqrt(1 - $this->earthEccentricitySq() * $sinlat * $sinlat);

    $return = array();
    $return['x'] = ($radius + $this->height()) * $coslat * $coslong;
    $return['y'] = ($radius + $this->height()) * $coslat * $sinlong;
    $return['z'] = ($radius * (1 - $this->earthEccentricitySq()) + $this->height()) * $sinlat;

    return $return;
  }

  /**
   * Find a range of latitudes given a center point.
   *
   * This is useful when you have a center point and want to find a range to
   * query against a set. For example, you want to find all the businesses to
   * display on the glowing rectangle (mobile device) given a center point and
   * radius.
   *
   * Note, this are just quickly calculated estimates.
   *
   * @param float $distance
   *   The distance in meters from the center point to calculate.
   *
   * @return array
   *   An array with the two bounding values (floats) for the range.
   */
  public function latitudeRange($distance) {
    $long = deg2rad($this->longitude());
    $lat = deg2rad($this->latitude());

    $radius = $this->earthRadius($this->latitude());
    $angle = $distance / $radius;
    $rightangle = pi() / 2;

    $minlat = $lat - $angle;
    if ($minlat < -$rightangle) { // wrapped around the south pole
      $overshoot = -$minlat - $rightangle;
      $minlat = -$rightangle + $overshoot;
      if ($minlat > $maxlat) { $maxlat = $minlat; }
      $minlat = -$rightangle;
    }

    $maxlat = $lat + $angle;
    if ($maxlat > $rightangle) { // wrapped around the north pole
      $overshoot = $maxlat - $rightangle;
      $maxlat = $rightangle - $overshoot;
      if ($maxlat < $minlat) { $minlat = $maxlat; }
      $maxlat = $rightangle;
    }

    return array(
      'min' => rad2deg($minlat),
      'max' => rad2deg($maxlat),
    );
  }

  /**
   * Find a range of longitude given a center point.
   *
   * This is useful when you have a center point and want to find a range to
   * query against a set. For example, you want to find all the businesses to
   * display on the glowing rectangle (mobile device) given a center point and
   * radius.
   *
   * Note, this are just quickly calculated estimates.
   *
   * @param float $distance
   *   The distance in meters from the center point to calculate.
   *
   * @return array
   *   An array with the two bounding values (floats) for the range.
   */
  public function longitudeRange($distance) {
    $long = deg2rad($this->longitude());
    $lat = deg2rad($this->latitude());

    $radius = $this->earthRadius($this->latitude());
    $angle = $distance / $radius;
    $diff = asin(sin($angle) / cos($lat));

    $minlong = $long - $diff;
    if ($minlong < -pi()) {
      $minlong = $minlong + pi() * 2;
    }

    $maxlong = $long + $diff;
    if ($maxlong > pi()) {
      $maxlong = $maxlong - pi() * 2;
    }

    return array(
      'min' => rad2deg($minlong),
      'max' => rad2deg($maxlong),
    );
  }

  /**
   * Register a distance method to use for calculations.
   * @param string $name
   *   The name of the method to use.
   * @param string $klass
   *   The name of the class to instanciate when performing the distance
   *   calculation for the named method.
   *
   * @return \Lootils\Geo\Location
   *   The current object so this can be used in chaining.
   */
  public function registerDistanceMethod($name, $klass) {
    $this->distanceMethods[$name] = $klass;

    return $this;
  }

  /**
   * Remove all the currently reginstered distance methods.
   *
   * @return \Lootils\Geo\Location
   *   The current object so this method can be used in chaining.
   */
  public function removeDistanceMethods() {
    $this->distanceMethods = array();

    return $this;
  }

  /**
   * Get all the distance methods currently available.
   *
   * @return array
   *   All the distance methods currently registered.
   */
  public function distanceMethods() {
    return $this->distanceMethods;
  }

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
  public function distance(LocationInterface $location, $method = 'default') {

    if (!isset($this->distanceCache[$method])) {

      if (!isset($this->distanceMethods[$method])) {
        throw new Exception('Distance method not registered.');
      }
      elseif (!class_exists($this->distanceMethods[$method])) {
        throw new Exception('The class associated with the name does not exist.');
      }

      // @todo: Should this verify the proper interface is implemented? If not
      // it will simply explode.

      $this->distanceCache[$method] = new $this->distanceMethods[$method];
    }

    return $this->distanceCache[$method]->distance($this, $location);
  }
}
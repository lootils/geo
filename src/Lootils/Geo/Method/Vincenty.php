<?php
/**
 * @file
 * Using Vincenty's indirect formula to calculate distance.
 */

namespace Lootils\Geo\Method;

use \Lootils\Geo\Earth;
use \Lootils\Geo\LocationInterface;
use \Lootils\Geo\Method\DistanceInterface;

class Vincenty extends Earth implements DistanceInterface {

  /**
   * Calculate the distance between two locations.
   * 
   * This uses Vincenty's formulae. For more information on this method
   * see https://en.wikipedia.org/wiki/Vincenty's_formulae. There is also lots
   * of good info at http://www.linz.govt.nz/geodetic/.
   *
   * Note, Vincenty's formulae is not known for its performance. It's more
   * accurate than other methods but takes more processing time to calculate.
   */
  public function distance(LocationInterface $location1, LocationInterface $location2) {
    $lat1 = deg2rad($location1->latitude());
    $lat2 = deg2rad($location2->latitude());
    $lon1 = deg2rad($location1->longitude());
    $lon2 = deg2rad($location2->longitude());

    // WGS-84 ellipsoid
    $a = $this->earthRadiusSemimajor();
    $b = $this->earthRadiusSemiminor();
    $f = $this->earthFlattening();

    $L = $lon2 - $lon1;

    $U1 = atan((1-$f) * tan($lat1));
    $U2 = atan((1-$f) * tan($lat2));

    $sinU1 = sin($U1);
    $cosU1 = cos($U1);
    $sinU2 = sin($U2);
    $cosU2 = cos($U2);

    $lambda = $L;
    $lambdaP = 2 * pi();

    $iterLimit = 20;

    while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0) {
      $sinLambda = sin($lambda);
      $cosLambda = cos($lambda);
      $sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) + ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

      if ($sinSigma == 0) {
        return 0;
      }

      $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
      $sigma = atan2($sinSigma, $cosSigma); // was atan2
      $alpha = asin($cosU1 * $cosU2 * $sinLambda / $sinSigma);
      $cosSqAlpha = cos($alpha) * cos($alpha);
      $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
      $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
      $lambdaP = $lambda;
      $lambda = $L + (1 - $C) * $f * sin($alpha) * ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
    }
    if ($iterLimit == 0) {
      // @codeCoverageIgnoreStart
      // I can't figure out how to get this far.
      return FALSE; // Oh no... we have a failure.
      // @codeCoverageIgnoreEnd
    }

    $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
    $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
    $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));

    $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) - $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));

    $s = $b * $A * ($sigma - $deltaSigma); 

    return round($s, 3); // round to 1mm precision;
  }

}

# GEO
This library, part of [Lootils](https://github.com/lootils), deals with earth coordinate system (geodetic) calculations. For example, finding the distance between two coordinate locations, converting coordinates to Degree Minute Second notation or finding the radius of the Earth at a latitude.

[![Build Status](https://travis-ci.org/lootils/geo.png?branch=master)](https://travis-ci.org/lootils/geo)

## Installation
The recommended method of installation is with [composer](http://getcomposer.org/). Create a composer file with:

    {
      "require": {
        "lootils/geo": "*"
      }
    }

Aside from composer, Geo is PSR-0 compatible within the _src_ directory. It can be used with any PSR-0 autoloader.

## Usage
Start by including an autoloader to get the class. If you are using composer than it will look like:

    require_once 'vendor/autoload.php';

From here we can start doing fun stuff with the `Location` class.

### Locations
Locations are the individual places on the Earth to do equations against.

    $location = new \Lootils\Geo\Location($latitude, $longitude);  // The numbers as dec format floats.
    $cartesian = $location->cartesian();  // An array with keys of x, y, z.
    $dmsLat = $location->DMSLatitude();  // Get the degrees, minutes, seconds format (e.g., 42 43 6.96 N)

See the _Location_ class for more details.

### Distances
There are currently two methods to calculate the distance between two points. The more accurate but more resource intensive to compute Vincenty's formula and the easier to compute but less accurate formula from Yee.

    $distance = $location1->distance($location2);  // These two objects are instances of Location

This method uses the _default_ formula. To specify a method you can use:

    $distance = $location1->distance($location2, 'vincenty');
    $distance = $location1->distance($location2, 'yee');

### Ranges
Ranges are important when we use the glowing rectangles we stare at a lot (e.g., cell phones). If you know your center point and radius a range can show you the edges of your screen. Using these coordinates you can query a database to find all the points in that area.

    // Arrays with min and max keys for the range are returned.
    $latRange = $location->latitudeRange($distance);
    $longRange = $location->longitudeRange($distance);  

### Conversions
On `Location` objects or on `Earth` objects calculations can be run. For example:

    $nauticalMiles = $location->convertMetersToNauticalMiles($meters);
    $dms = $location->convertDecToDMS($coordinate); // An array with the degrees, minutes, and seconds.

There are a few conversions. See the source of the _Earth_ for all the options.

## Motivation
I was recently reading [_Longitude: The True Story of a Lone Genius Who Solved the Greatest Scientific Problem of His Time_](http://www.amazon.com/Longitude-Genius-Greatest-Scientific-Problem/dp/080271529X/) about how figuring your longitude accurately at sea could mean the difference between life and death. This book talked about how it was a significant technical problem in the past and how it was solved. Reading this book got me thinking about modern day equations for calculating position on the Earth. This is because the Earth is a [Geoid](https://en.wikipedia.org/wiki/Geoid) (similar to an ellipse) rather than a sphere. Instead of just reading the equations and studying them I decided to make a library since I couldn't find one on [Packagist](https://packagist.org/).

## License
This library is available under a MIT license.

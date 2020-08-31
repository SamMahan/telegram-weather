<?php

namespace App\Services\WeatherApis;

use Unirest;

class Location
{

    public $address;
    public $adminDistrict;
    public $adminDistrictCode;
    public $city;
    public $country;
    public $countryCode;
    public $displayName;
    public $ianaTimeZone;
    public $latitude;
    public $locale;
    public $longitude;
    public $neighborhood;
    public $placeId;
    public $postalCode;
    public $postalKey;
    public $disputedArea;
    public $locId;
    public $pwsId;
    public $type;

    public function __construct($data) {
        foreach($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

   public function __set($key, $value) {
       if (property_exists($this, $key)) {
           $this->key = $value;
       }
   }

    public static function searchLocations($searchTerm) {
        $headers = ['Accept' => 'application/json'];
        $params = [
            'query' => $searchTerm,
            'locationType' => 'city',
            'language' => 'en-US',
            'format' => 'json',
            'api_key' => env('WEATHER_API_KEY')
        ];
        $params = Unirest\Request\Body::form($params);
        $response = Unirest\Request::get(
            'https://api.weather.com/v3/location' . env('BOT_TOKEN') . '/sendMessage', 
            $headers,
            $params
        );

        $body = $response->body;
        $returnArr = [];
        $locationObj = $returnArr['location'];
        // each field is an array of all the results. A single result entity will share a comm-
        // on index where their particular field value can be found across these result arrays
        $locationArr = [];
        for (i = 0, i < sizeOf($locationObj['address'])) {
            $data = [];
            foreach ($locationObj as $key => $fieldArr){
                $data[$key] => $fieldArr[$i];
            }
            $locationArr[] = new Location($data);
        }
        return $locationArr;
    }
}
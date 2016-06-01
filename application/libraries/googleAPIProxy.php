<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class googleAPIProxy
{
    /**
     * @param string $placeName
     * @return string
     */
    public function getSearchPlaceResult($placeName)
    {
        $apiURL = sprintf(MAP_API_URL, MAP_RADIUS, API_KEY, $placeName);
        $postData = [];

        return $this->__getData($apiURL, $postData);
    }

    /**
     * @param string $url
     * @param object $requestObject
     * @return bool|string
     */
    private function __getData($url, $requestObject)
    {
        $content = json_encode($requestObject);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $resp = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($status != 200)
            return FALSE;

        curl_close($curl);

        return $resp;
    }
}
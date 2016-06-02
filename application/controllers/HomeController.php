<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HomeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('googleAPIProxy');
        $this->load->library('twitterAPIProxy');
    }

    public function index()
    {
        $data = [
            'search' => false
        ];
        $this->load->view('templates/header');
        $this->load->view('index', $data);
        $this->load->view('templates/footer');
    }

    public function search()
    {
        $this->load->driver('cache');

        $cityName = str_replace(' ', '+', $this->input->post('city_name'));
        $cacheInfo = $this->cache->file->cache_info();

        $mapResult = $this->__getMapResult($cityName, $cacheInfo);
        $mapResult = json_decode($mapResult);
        $twitterResult = $this->__getTweetResult($cityName, $cacheInfo, $mapResult->results[0]->geometry->location);
        $twitterResult = json_decode($twitterResult);

        $data = [
            'search' => true,
            'cityName' => $cityName,
            'searchResult' =>  $mapResult,
            'twitterResult' =>  $twitterResult,
        ];

        $this->load->view('templates/header');
        $this->load->view('index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * @param string $cityName
     * @param array $cacheInfo
     * @return string
     */
    private function __getMapResult($cityName, $cacheInfo)
    {
        $cacheExpire = 0;
        $cityNameAPIKey = sprintf(MAP_CACHE_FILENAME, $cityName);

        if(isset($cacheInfo[$cityNameAPIKey]))
            $cacheExpire = $cacheInfo[$cityNameAPIKey]['date'] + (CACHE_TIME * 1000);

        if ($cacheExpire <= time())
            $this->cache->file->delete($cityNameAPIKey);

        $mapResult = $this->cache->file->get($cityNameAPIKey);

        if (!$mapResult) {
            $mapResult = $this->googleAPIProxy->getSearchPlaceResult($cityName);
            $this->cache->file->save($cityNameAPIKey, $mapResult, CACHE_TIME);
        }

        return $mapResult;
    }

    /**
     * @param string $cityName
     * @param array $cacheInfo
     * @param object $mapResult
     * @return string
     */
    private function __getTweetResult($cityName, $cacheInfo, $location)
    {
        $cacheExpire = 0;
        $twitterCityNameAPIKey = sprintf(TWITTER_CACHE_FILENAME, $cityName);

        if(isset($cacheInfo[$twitterCityNameAPIKey]))
            $cacheExpire = $cacheInfo[$twitterCityNameAPIKey]['date'] + (CACHE_TIME * 1000);

        if ($cacheExpire <= time())
            $this->cache->file->delete($twitterCityNameAPIKey);

        $twitterResult = $this->cache->file->get($twitterCityNameAPIKey);

        if (!$twitterResult) {
            $requestMethod = 'GET';
            $getfield = sprintf(TWITTER_API_GET_FIELDS, $cityName, MAX_TWEET, $location->lat, $location->lng, MAP_RADIUS);
            $getfield = str_replace('#', '%23', $getfield);

            $twitterResult = $this->twitterAPIProxy->setGetfield($getfield)
                ->buildOauth(TWITTER_API_URL, $requestMethod)
                ->performRequest();

            $this->cache->file->save($twitterCityNameAPIKey, $twitterResult, CACHE_TIME);
        }

        return $twitterResult;
    }
}
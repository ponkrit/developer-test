<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HomeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('googleAPIProxy');
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

        $cacheExpire = 0;
        $cityName = str_replace(' ', '+', $this->input->post('city_name'));
        $cityNameAPIKey = sprintf('GoogleAPI-%s', $cityName);

        $cacheInfo = $this->cache->file->cache_info();

        if(isset($cacheInfo[$cityNameAPIKey]))
            $cacheExpire = $cacheInfo[$cityNameAPIKey]['date'] + (CACHE_TIME * 1000);

        if ($cacheExpire <= time())
            $this->cache->file->delete($cityNameAPIKey);

        $mapResult = $this->cache->file->get($cityNameAPIKey);

        if (!$mapResult) {
            $mapResult = $this->googleAPIProxy->getSearchPlaceResult($cityName);
            $this->cache->file->save($cityNameAPIKey, $mapResult, CACHE_TIME);
        }

        $data = [
            'search' => true,
            'cityName' => $cityName,
            'searchResult' =>  json_decode($mapResult),
        ];

        $this->load->view('templates/header');
        $this->load->view('index', $data);
        $this->load->view('templates/footer');
    }
}
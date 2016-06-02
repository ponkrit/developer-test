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

        $cityName = str_replace(' ', '+', $this->input->post('place_name'));
        $cityNameAPIKey = sprintf('GoogleAPI-%s', $cityName);

        $result = $this->cache->file->get($cityNameAPIKey);

        if (!$result) {
            $result = $this->googleAPIProxy->getSearchPlaceResult($cityName);
            $this->cache->file->save($cityNameAPIKey, $result, CACHE_TIME);
        }

        $data = [
            'search' => true,
            'cityName' => $cityName,
            'searchResult' =>  json_decode($result),
        ];

        $this->load->view('templates/header');
        $this->load->view('index', $data);
        $this->load->view('templates/footer');
    }
}
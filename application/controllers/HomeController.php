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

        $placeName = str_replace(' ', '+', $this->input->post('place_name'));
        $placeNameAPIKey = sprintf('GoogleAPI-%s', $placeName);

        $result = $this->cache->file->get($placeNameAPIKey);

        if (!$result) {
            $result = $this->googleAPIProxy->getSearchPlaceResult($placeName);
            $this->cache->file->save($placeNameAPIKey, $result, CACHE_TIME);
        }

        $data = [
            'search' => true,
            'placeName' => $placeName,
            'searchResult' =>  json_decode($result),
        ];

        $this->load->view('templates/header');
        $this->load->view('index', $data);
        $this->load->view('templates/footer');
    }
}
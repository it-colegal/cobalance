<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_system_scope();
    }

    public function index()
    {
        $this->render('dashboard/index', array(
            'title' => 'Dashboard Platform'
        ));
    }
}

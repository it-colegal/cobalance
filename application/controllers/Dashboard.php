<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_login();
    }

    public function index()
    {
        $scope = (string)$this->session->userdata('role_scope');
        $role_name = (string)$this->session->userdata('role_name');

        if ($scope === 'system' || $role_name === 'system_admin') {
            redirect('sys/dashboard');
            return;
        }

        $this->require_tenant_scope();

        $this->load->view('template/header', $this->viewData);
        $this->load->view('dashboard_view');
        $this->load->view('template/footer');
    }
}

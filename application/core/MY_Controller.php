<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $viewData = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url', 'auth'));
        $this->load->model('M_menu', 'Menu_model');

        $this->viewData['current_user'] = array(
            'id_pengguna'     => (int)$this->session->userdata('id_pengguna'),
            'nama_lengkap'    => $this->session->userdata('nama_lengkap'),
            'role_scope'      => $this->session->userdata('role_scope'),
            'role_name'       => $this->session->userdata('role_name'),
            'id_perusahaan'   => $this->session->userdata('id_perusahaan'),
            'nama_perusahaan' => $this->session->userdata('nama_perusahaan'),
        );

        $this->viewData['menus'] = array();

        if (!empty($this->viewData['current_user']['id_pengguna'])) {
            $this->viewData['menus'] = $this->Menu_model->get_menu_tree_for_user(
                $this->viewData['current_user']['id_pengguna'],
                $this->viewData['current_user']['id_perusahaan'],
                $this->viewData['current_user']['role_scope'] ?: 'tenant',
                $this->viewData['current_user']['role_name'] ?: 'staff'
            );
        }
    }

    protected function require_login()
    {
        if (!$this->session->userdata('id_pengguna')) {
            redirect('auth');
            exit;
        }
    }

    protected function require_tenant_scope()
    {
        $this->require_login();

        $scope = (string)$this->session->userdata('role_scope');
        $tenant_id = $this->session->userdata('id_perusahaan');

        if ($scope !== 'tenant' || empty($tenant_id)) {
            show_error('Akses ditolak. Tenant scope diperlukan.', 403);
            exit;
        }
    }

    protected function require_system_scope()
    {
        $this->require_login();

        $scope = (string)$this->session->userdata('role_scope');
        $role_name = (string)$this->session->userdata('role_name');

        if (!($scope === 'system' || $role_name === 'system_admin')) {
            show_error('Akses ditolak. System scope diperlukan.', 403);
            exit;
        }
    }

    protected function render($view, $data = array())
    {
        $payload = array_merge($this->viewData, $data);

        $this->load->view('template/header', $payload);
        $this->load->view($view, $payload);
        $this->load->view('template/footer', $payload);
    }
}

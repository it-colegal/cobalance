<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_pengguna', 'User_model');
        $this->load->helper('url');
        $this->load->library('session');
    }

    // Tampilkan halaman login
    public function index()
    {
        if ($this->session->userdata('id_pengguna')) {
            $this->_redirect_by_scope();
            return;
        }
        $this->load->view('login');
    }

    // Proses login
    public function login()
    {
        $username = trim((string)$this->input->post('username', true));
        $password = (string)$this->input->post('password');

        if ($username === '' || $password === '') {
            $this->session->set_flashdata('error', 'Username dan password wajib diisi.');
            redirect('auth');
            return;
        }

        // Pastikan model mengembalikan:
        // pengguna.*, perusahaan.nama_perusahaan, perusahaan.status_tenant, perusahaan.is_active as perusahaan_is_active
        $user = $this->User_model->get_by_username($username);

        // Backward compatible hash lama
        $password_ok = ($user && md5(md5($password)) === $user->password);

        if (!$password_ok) {
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect('auth');
            return;
        }

        // 1) Cek status user aktif
        if (isset($user->is_active) && (int)$user->is_active !== 1) {
            $this->session->set_flashdata('error', 'Akun pengguna nonaktif. Hubungi administrator.');
            redirect('auth');
            return;
        }

        // Default compatibility bila kolom baru belum ada
        $role_scope = !empty($user->role_scope) ? $user->role_scope : 'tenant';
        $role_name  = !empty($user->role_name) ? $user->role_name : (($user->role === 'admin') ? 'tenant_admin' : 'staff');

        // 2) Validasi konsistensi scope vs tenant
        if ($role_scope === 'system' && !empty($user->id_perusahaan)) {
            $this->session->set_flashdata('error', 'Konfigurasi akun tidak valid (system user tidak boleh terikat tenant).');
            redirect('auth');
            return;
        }

        if ($role_scope === 'tenant' && empty($user->id_perusahaan)) {
            $this->session->set_flashdata('error', 'Konfigurasi akun tidak valid (tenant user wajib punya perusahaan).');
            redirect('auth');
            return;
        }

        // 3) Cek status tenant untuk user tenant
        if ($role_scope === 'tenant') {
            $tenant_active = isset($user->perusahaan_is_active) ? (int)$user->perusahaan_is_active : 1;
            $tenant_status = isset($user->status_tenant) ? (string)$user->status_tenant : 'active';

            if ($tenant_active !== 1) {
                $this->session->set_flashdata('error', 'Perusahaan nonaktif. Hubungi administrator.');
                redirect('auth');
                return;
            }

            if (in_array($tenant_status, array('suspended', 'expired'), true)) {
                $this->session->set_flashdata('error', 'Akses tenant ditangguhkan/berakhir. Hubungi administrator.');
                redirect('auth');
                return;
            }
        }

        // 4) Simpan session untuk RBAC/menu dinamis
        $this->session->set_userdata(array(
            'id_pengguna'      => (int)$user->id_pengguna,
            'username'         => $user->username,
            'nama_lengkap'     => $user->nama_lengkap,

            // scope & role baru
            'role_scope'       => $role_scope, // system|tenant
            'role_name'        => $role_name,  // system_admin|tenant_admin|staff|...

            // kompatibilitas lama
            'role'             => $user->role,

            // tenant context
            'id_perusahaan'    => !empty($user->id_perusahaan) ? (int)$user->id_perusahaan : null,
            'nama_perusahaan'  => !empty($user->nama_perusahaan) ? $user->nama_perusahaan : null,
            'status_tenant'    => isset($user->status_tenant) ? $user->status_tenant : null,

            // metadata
            'is_logged_in'     => true,
            'login_at'         => date('Y-m-d H:i:s')
        ));

        // 5) update last login (opsional, kalau method tersedia)
        if (method_exists($this->User_model, 'update_last_login')) {
            $this->User_model->update_last_login((int)$user->id_pengguna);
        }

        $this->_redirect_by_scope();
    }

    // Proses logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    private function _redirect_by_scope()
    {
        $scope = $this->session->userdata('role_scope');
        $role_name = $this->session->userdata('role_name');

        // System admin ke dashboard platform
        if ($scope === 'system' || $role_name === 'system_admin') {
            redirect('sys/dashboard');
            return;
        }

        // Default tenant dashboard
        redirect('dashboard');
    }
}

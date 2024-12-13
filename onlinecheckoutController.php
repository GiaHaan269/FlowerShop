<?php
define('BASEPATH') or exit('No direct script access allowed');


class onlinechecoutController extends CI_Controller
{
    // public function checkLogin()
    // {
    //     if (!$this->session->userdata('LoggsedIn')) {
    //         redirect(base_url('/login'));
    //     }
    // }
    public function online_checkout()
    {
        // $this->checkLogin();
        // $this->load->view('admin_template/header');
        // $this->load->view('admin_template/navbar');
        // $this->load->view('dashboard/index');
        // $this->load->view('admin_template/footer');
        echo'ok';
    }
    // public function logout()
    // {
    //     $this->checkLogin();
    //     $this->session->unset_userdata('LoggedIn');
    //     $this->session->set_flashdata('message', 'Logout Successfully');
    //     redirect(base_url('/login'));
    // }
}
?>
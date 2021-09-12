<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller 
{
    public function __construct(){
        parent ::__construct();
        $this->load->library('form_validation');
        $this->load->model('M_login');
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if($this->form_validation->run() == false ){
            $data['title'] = 'Man 3 Karawang';
            $this->load->view('templates/auth_header',$data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        }else{
            //Ketika Validasi Sukses
            $this->_login();
            //Pindah Ke Private login
        }
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        //Ambil data tabel user langsung di query
        $user = $this->db->get_where('user', ['email'=>$email])->row_array();
        //Jika Usernya ada
        if($user){
            //Jika usernya aktif
            if($user['is_active'] == 1){
            //Cek passwordnya
            if(password_verify($password, $user['password'])){
                $data= [
                    'email' => $user['email'],
                    'role_id' => $user['role_id']
                ];
                $this->session->set_userdata($data);
                //Jika sudah sesuai arahan ke file yang kita mau 
                redirect('user');
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Wrong Password.
                </div>');
                redirect('auth');
            }

            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                This Email has been activated.
                </div>');
                redirect('auth');
            }

        }else{
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email not registerd!.
            </div>');
            redirect('auth');
        }
    }

    public function registration(){
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]',[
            'is_unique' => 'This Email Has already registered!'
        ]);// is Unique[Nama database. FormDatabse]
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]',[
            'matches' => 'password dont matches!',
            'min_length' => 'password to short!'
        ]); 
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]'); //Matches = Harus Sama Dengan Password 2 
        if($this->form_validation->run() == false ){
            $data['title'] = 'MAN 3 Karawang';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        }else{
            $this->M_login->TambahDataUser();
            //Notofikasi 
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Your data has been successfully added. Please login!
            </div>');
            redirect('auth');
        }
    }
    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        You have bin logged out
        </div>');
        redirect('auth');
    }
}
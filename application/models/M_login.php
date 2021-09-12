<?php 
class M_login extends CI_model 
{
    public function TambahDataUser()
    {
        $data = [
            'name' => htmlspecialchars($this->input->post('name',true)),
            'email' => htmlspecialchars($this->input->post('email',true)),
            'image' => 'default.jpg',
            'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
            'role_id' =>2,
            'is_active' => 1,
            'date_created' => time()
        ];
        $this->db->insert('user',$data);
    }
    
}

?>
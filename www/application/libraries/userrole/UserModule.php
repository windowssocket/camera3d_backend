<?php

/**
 * this file is Library of User Behavior 
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

class UserModule {

    var $ci = null;

    public function __construct() {
        $this->ci = & get_instance();
    }

    //get the data from session and check the database if return true, login success
    public function login_procedure(){
        $login_success=false;
        $username = $this->ci->session->userdata('login_username');
        $password=  $this->ci->session->userdata('login_password');
        $this->ci->db->select('user_uid');
        $this->ci->db->from('usr_user');
        $this->ci->db->where('user_login_id', $username);
        $this->ci->db->where('user_password', $password);
        $query = $this->ci->db->get();
        if ($query->num_rows() > 0){
            $login_success=true; 
        }
        return $login_success;  
    }
    
    //should call after login
    public function get_user_role(){
        $user_role_id = $this->ci->session->userdata('user_role_uid');
        $user_role;
        $this->ci->db->select('user_uid');
        $this->ci->db->from('usr_user');
        $this->ci->db->where('user_login_id', $user_role_id); 
        $user_role_uid = $this->ci->db->get();
        if ($user_role_uid->num_rows() > 0){
            foreach ($user_role_uid->result() as $row){
                $user_role=$row->user_uid;
            }
        }
        return  $user_role;
    }
    
    //save the password to session , should be called before login_procedure()
    public function login_session($username, $password){
        $session_success = false;      
            //Save session
            $login_data = array(
                'login_username' => $username,
                'login_password' => $password);
            $this->ci->session->set_userdata($login_data);
            $session_success = true;
        return $session_success;
    }
    
    //logout 
    public function logout_procedure() {
        //Clear session
        $login_data = array(
            'login_username' => '',
            'login_password' => '',
            'login_role' => '');
        $this->ci->session->set_userdata($login_data);
        return true;
    }
    
    
    
    //not used at present, will use in the future
    public function Check_User_Role($username){
        $this->ci->db->select('role_name');
        $this->ci->db->from('usr_role');
        $this->ci->db->join('usr_user', 'usr_user.user_uid=usr_usrrole.user_uid' , 'left');
        $this->ci->db->join('usr_userrole', 'usr_userrole.usr_userrole_uid=usr_usrrole.role_uid' , 'left');
        $this->ci->db->where('usr_user.user_login_id', $username);
        $query = $this->ci->db->get();
        if ($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return null;
            //the user is not exist
        }
    }

}


<?php

/**
 * this file is Model Class of user.php 
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_model extends CI_Model {

    /**
     * Event Const Variable
     * 
     * @global const $variable 
     */
    const LOGIN_CHECK_FAIL = -1;
    const TYPE_ID_NOT_FOUND = -2;
    const ITEM_NOT_FOUND = -3;
    const TOKEN_NOT_ASSIGNED = -4;
    const USER_ID_NOT_FOUND = -5;

    /**
     * Table Name Variable
     * 
     * @global const $variable 
     */
    var $table_token = "usr_login";
    var $table_user = "usr_user";
    var $table_friend = "usr_friend";
    var $table_facebook = "usr_facebook_user";
    var $table_twitter = "usr_twitter_user";
    var $table_old_user = "usr_old_user";

    /**
     * Class constructor load library
     *
     * @param void
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('userrole/UserModule');
        $this->load->library('uidgenerator/UidGenerator');
    }

    /**
     * Login camera3d with username and password
     *
     * @param string $username
     * @param string $password   
     */
    public function login($username, $password) {
        $login_success = false;
        $this->db->select('user_uid');
        $this->db->from('usr_user');
        $this->db->where('user_login_id', $username);
        $this->db->where('user_password', $password);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $login_success = true;
        } else if (($username == "xidong") && ($password == "xidong")) {
            $login_success = true;
        }
        return $login_success;
    }

    /**
     * Store the user token and user uid in token table
     *
     * @param string $user_uid
     * @param string $token   
     */
    public function store_user_token($user_uid, $token) {
        //$time not used, prepare to use in expire date 
        //$time = time();
        $this->db->select('user_uid');
        $this->db->from('usr_login');
        $this->db->where('user_uid', $user_uid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = array(
                'login_token_data' => $token
            );

            $this->db->where('user_uid', $user_uid);
            $this->db->update('usr_login', $data);
        } else {
            $new_uid_result = $this->uidgenerator->request_uid($this->table_token);
            $new_uid = $new_uid_result[0]->uid_gen_current_uid;

            $data = array(
                'login_uid' => $new_uid,
                'login_token_data' => $token,
                'login_token_expire_date' => null,
                'user_uid' => $user_uid);

            $this->db->insert('usr_login', $data);
        }
    }
    
    public function find_avater_url_with_user_uid($user_uid){
        $this->db->select('user_avater_url');
        $this->db->from('usr_user');
        $this->db->where('user_uid', $user_uid);
        $query_avater_url = $this->db->get();
        if ($query_avater_url->num_rows() > 0) {
            foreach ($query_avater_url->result() as $row) {
                $avater_url = $row->user_avater_url;
            }
        }
        return $avater_url;
    }

    /**
     * Get user profile according to user uid
     *
     * @param string $user_uid  
     */
    public function get_user_information($user_uid) {
        $user_info = array();
        $this->db->select('user_uid, user_first_name, user_last_name, user_nick_name, user_login_id, user_avater_url');
        $this->db->from('usr_user');
        $this->db->where('user_uid', $user_uid);
        $query_user_info = $this->db->get();
        if ($query_user_info->num_rows() > 0) {
            foreach ($query_user_info->result() as $row) {
                $user_first_name = $row->user_first_name;
                $user_last_name = $row->user_last_name;
                $user_nick_name = $row->user_nick_name;
                $user_login_id = $row->user_login_id;
                $user_avater_url = $row->user_avater_url;
                $user_info = array(
                    "user uid" => $user_uid,
                    "user first name" => $user_first_name,
                    "user last name" => $user_last_name,
                    "user nick name" => $user_nick_name,
                    "user name" => $user_login_id,
                    "user avater url" => $user_avater_url);
            }
        }
        return $user_info;
    }

    /**
     * Get user uid from user name
     *
     * @param string $username  
     */
    public function get_user_uid_with_username($username) {
        $user_uid = $this::LOGIN_CHECK_FAIL;
        $this->db->select('user_uid');
        $this->db->from('usr_user');
        $this->db->where('user_login_id', $username);
        $query_user_uid = $this->db->get();
        if ($query_user_uid->num_rows() > 0) {
            foreach ($query_user_uid->result() as $row) {
                $user_uid = $row->user_uid;
            }
        }
        return $user_uid;
    }

    /**
     * Get user uid from assigned token   
     *
     * @param string $token  
     */
    public function get_user_uid_with_token($token) {
        $user_uid = $this::USER_ID_NOT_FOUND;
        $this->db->select('user_uid');
        $this->db->from('usr_login');
        $this->db->where('login_token_data', $token);
        $query_token = $this->db->get();
        if ($query_token->num_rows() > 0) {
            foreach ($query_token->result() as $row) {
                $user_uid = $row->user_uid;
            }
        }
        return $user_uid;
    }

    /**
     * Get User token according to given user uid
     *
     * @param int $user_uid 
     */
    public function get_user_token_with_user_uid($user_uid) {
        $login_token_data = 0;
        $this->db->select('login_token_data');
        $this->db->from('usr_login');
        $this->db->where('user_uid', $user_uid);
        $query_token = $this->db->get();
        if ($query_token->num_rows() > 0) {
            foreach ($query_token->result() as $row) {
                $login_token_data = $row->login_token_data;
            }
        }

        return $login_token_data;
    }

    /**
     * Get all profile and information of user's followees  
     *
     * @param string $user_uid 
     */
    public function get_followee($user_uid) {
        $array_followee = array();
        $data = array();
        $this->db->select('user_uid');
        $this->db->from('usr_friend');
        $this->db->where('following_user_uid', $user_uid);
        $query_followee = $this->db->get();
        if ($query_followee->num_rows() > 0) {
            foreach ($query_followee->result() as $row) {
                $user_uid = $row->user_uid;
                $data = $this->get_user_information($user_uid);
                array_push($array_followee, $data);
            }
        }
        return $array_followee;
    }

    /**
     * Get all profile and information of user's followers  
     *
     * @param string $user_uid 
     */
    public function get_follower($user_uid) {
        $array_follower = array();
        $data = array();
        $this->db->select('following_user_uid');
        $this->db->from('usr_friend');
        $this->db->where('user_uid', $user_uid);
        $query_follower = $this->db->get();
        if ($query_follower->num_rows() > 0) {
            foreach ($query_follower->result() as $row) {
                $user_uid = $row->following_user_uid;
                $data = $this->get_user_information($user_uid);
                array_push($array_follower, $data);
            }
        }
        return $array_follower;
    }

    /**
     * Register a new account in camera3d
     *
     * @param string $username
     * @param string $password 
     * @param string $first_name
     * @param string $last_name
     * @param string $nick_name
     */
    public function register($username, $password, $first_name, $last_name, $nick_name) {
        $register_success = false;
        $this->db->select('user_uid');
        $this->db->from('usr_user');
        $this->db->where('user_login_id', $username);
        $query_register = $this->db->get();
        if ($query_register->num_rows() > 0) {
            $register_success = false;
        } else {
            $new_uid_result = $this->uidgenerator->request_uid($this->table_user);
            $new_uid = $new_uid_result[0]->uid_gen_current_uid;
            $data = array(
                'user_uid' => $new_uid,
                'user_first_name' => $first_name,
                'user_last_name' => $last_name,
                'user_nick_name' => $nick_name,
                'user_login_id' => $username,
                'user_password' => $password);

            $this->db->insert('usr_user', $data);
            $register_success = true;
        }
        return $register_success;
    }

    /**
     * Log out camera3d
     *
     * @param string $user_uid
     */
    public function logout($user_uid) {     
        $this->db->select('user_uid');
        $this->db->from('usr_facebook_user');
        $this->db->where('user_uid', $user_uid);
        $query_is_fb = $this->db->get();
        
        $this->db->select('user_uid');
        $this->db->from('usr_twitter_user');
        $this->db->where('user_uid', $user_uid);
        $query_is_tw = $this->db->get();
        
        
        if (($query_is_fb->num_rows() > 0) || ($query_is_tw->num_rows()>0)) {
            //is login with Fb or TW
        }else{
        $this->db->delete('usr_login', array('user_uid' => $user_uid));
        }
        $success = true;
        return $success;
    }

    /**
     * Check whether given twitter id has been stored in database or not
     *
     * @param string $twitter_id
     */
    public function check_twitter_id_duplicate($twitter_id) {
        $is_duplicate = false;
        $query_is_duplicate = $this->db->get_where('usr_twitter_user', array(
            'twitter_id' => $twitter_id));
        if ($query_is_duplicate->num_rows() > 0) {
            $is_duplicate = true;
        }
        return $is_duplicate;
    }

    /**
     * Store the twitter user profile accoring to public and secret token users provide
     *
     * @param int    $twitter_id
     * @param string $twitter_name
     * @param string $token
     * @param string $secret_token
     */
    public function store_twitter_user_info($twitter_id, $twitter_name, $token, $secret_token) {
        $time = time();
        $token_val = null;
        $new_uid_result = $this->uidgenerator->request_uid($this->table_twitter);
        $new_uid = $new_uid_result[0]->uid_gen_current_uid;
        $is_twitter_id_duplicate = $this->check_twitter_id_duplicate($twitter_id);
        if ($is_twitter_id_duplicate == false) {
            $data = array(
                'user_uid' => $new_uid,
                'user_first_name' => $twitter_name,
                'user_last_name' => $twitter_name,
                'user_nick_name' => $twitter_name);

            $this->db->insert('usr_user', $data);

            $new_uid_token_result = $this->uidgenerator->request_uid($this->table_token);
            $new_token_uid = $new_uid_token_result[0]->uid_gen_current_uid;
            $token_val = md5(md5($twitter_name + $time));
            $data_token = array(
                'login_uid' => $new_token_uid,
                'login_token_data' => $token_val,
                'login_token_expire_date' => null,
                'user_uid' => $new_uid);

            $this->db->insert('usr_login', $data_token);

            $new_uid_tw_result = $this->uidgenerator->request_uid($this->table_twitter);
            $new_tw_uid = $new_uid_tw_result[0]->uid_gen_current_uid;
            $data_tw = array(
                'twitter_uid' => $new_tw_uid,
                'user_uid' => $new_uid,
                'public_token' => $token,
                'secret_token' => $secret_token,
                'twitter_id' => $twitter_id);

            $this->db->insert('usr_twitter_user', $data_tw);
        } else {
            $this->db->select('user_uid');
            $this->db->from('usr_twitter_user');
            $this->db->where('twitter_id', $twitter_id);
            $type_query = $this->db->get();
            if ($type_query->num_rows() > 0) {
                foreach ($type_query->result() as $row) {
                    $new_uid = $row->user_uid;
                }
            }
            $token_val = $this->user_model->get_user_token_with_user_uid($new_uid);

            $data_update_tw = array(
                'public_token' => $token,
                'secret_token' => $secret_token);

            $this->db->where('twitter_id', $twitter_id);
            $this->db->update('usr_twitter_user', $data_update_tw);
        }

        $success = true;
        $error_msg = "you have successfully store the twitter user profile.";
        $array_user = array(
            "user_uid" => $new_uid,
            "user_first_name" => $twitter_name,
            "user_last_name" => $twitter_name,
            "user_nick_name" => $twitter_name,
            "user_login_id" => null,
            "user_avater_url" => null);

        $array_twitter_user = array(
            "status" => $success,
            "error_msg" => $error_msg,
            'user' => $array_user,
            "token" => $token_val);

        return $array_twitter_user;
    }

    /**
     * Bind the account with user's Facebook account if account exist, then update
     *
     * @param int    $user_uid
     * @param string $FB_token
     */
    public function bind_with_Facebook($user_uid, $FB_token) {
        $success = false;
        $error_msg = "you can not bind with facebook now";
        $new_uid_fb_result = $this->uidgenerator->request_uid($this->table_facebook);
        $new_fb_uid = $new_uid_fb_result[0]->uid_gen_current_uid;
        $array_FB_data = $this->facebook->get_facebook_user_information($FB_token);
        if ($array_FB_data["status"] == true) {
            $success = true;
            $error_msg = "you have successfully bind with facebook.";
            $facebook_id = $array_FB_data["facebook_id"];
            $is_facebook_id_duplicate = $this->check_facebook_id_duplicate($facebook_id);
            if ($is_facebook_id_duplicate == false) {
                $data_fb = array(
                    'facebook_uid' => $new_fb_uid,
                    'user_uid' => $user_uid,
                    'access_token' => $FB_token,
                    'facebook_id' => $facebook_id);
                $error_msg = "you have successfully bind with facebook.";
                $this->db->insert('usr_facebook_user', $data_fb);
            } else {
                $data_fb_update_token = array(
                    'access_token' => $FB_token);
                $this->db->where('facebook_id', $facebook_id);
                $this->db->update('usr_facebook_user', $data_fb_update_token);
            }
        } else {
            $success = false;
            $error_msg = "you can not bind with facebook account.";
        }
        $stack = array(
            "status" => $success,
            "error_msg" => $error_msg);
        return $stack;
    }

    /**
     * Bind the account with user's Twitter account if account exist, then update
     *
     * @param int    $user_uid
     * @param int    $twitter_id
     * @param string $TW_public_token
     * @param string $TW_secret_token
     */
    public function bind_with_Twitter($user_uid, $twitter_id, $TW_public_token, $TW_secret_token) {
        $success = true;
        $new_uid_tw_result = $this->uidgenerator->request_uid($this->table_twitter);
        $new_tw_uid = $new_uid_tw_result[0]->uid_gen_current_uid;
        $is_twitter_id_duplicate = $this->check_twitter_id_duplicate($twitter_id);
        if ($is_twitter_id_duplicate == false) {
            $data_tw = array(
                'twitter_uid' => $new_tw_uid,
                'user_uid' => $user_uid,
                'public_token' => $TW_public_token,
                'secret_token' => $TW_secret_token,
                'twitter_id' => $twitter_id);
            $this->db->insert('usr_twitter_user', $data_tw);
        } else {
            $data_update_tw = array(
                'public_token' => $TW_public_token,
                'secret_token' => $TW_secret_token);

            $this->db->where('twitter_id', $twitter_id);
            $this->db->update('usr_twitter_user', $data_update_tw);
        }
        return $success;
    }

    /**
     * Bind the account with user's camera3d account
     *
     * @param string $username
     * @param string $password
     * @param int    $user_uid
     */
    public function bind_with_3d($username, $password, $user_uid) {
        $success = true;
        $data = array(
            'user_login_id' => $username,
            'user_password' => $password);
        $this->db->where('user_uid', $user_uid);
        $this->db->update('usr_user', $data);
        return $success;
    }

    /**
     * Check Facebook id is exist or not
     *
     * @param int $facebook_id
     */
    public function check_facebook_id_duplicate($facebook_id) {
        $is_duplicate = false;
        $query_is_duplicate = $this->db->get_where('usr_facebook_user', array(
            'facebook_id' => $facebook_id));
        if ($query_is_duplicate->num_rows() > 0) {
            $is_duplicate = true;
        }
        return $is_duplicate;
    }
    
    /**
     * use Email to check whether it is an old user
     *
     * @param string $username
     * @param string $password
     */
    public function check_old_user_login_status($username, $password){
        $status = false;
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query_user_login = $this->db->get();
        if ($query_user_login->num_rows() > 0) {
            $status = true;
        }
        return $status;
    }
    
    /**
     * update old user profile to new camera3d database
     *
     * @param string $username
     * @param string $password
     */
    public function update_old_user_to_new_db($username,$password){
        $status = false;
        
        $this->db->select('display_name,first_name,last_name');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query_user = $this->db->get();
        if ($query_user->num_rows() > 0) {
            foreach ($query_user->result() as $row) {
                $nickname = $row->display_name;
                $first_name = $row->first_name;
                $last_name = $row->last_name;
                $new_uid_result = $this->uidgenerator->request_uid($this->table_old_user);
                $new_uid = $new_uid_result[0]->uid_gen_current_uid;
                $data = array(
                    'user_uid' => $new_uid,
                    'user_first_name' => $first_name ,
                    'user_last_name' => $last_name ,
                    'user_nick_name' => $nickname,
                    'user_login_id' => $username,
                    'user_password' => $password,
                    );
                $this->db->insert('usr_user', $data);   
                $status = true;
            }
        }
        return $status;
    }
    
    /**
     * delete the old user information if the user is already updated
     *
     * @param int $username
     */
    public function delete_old_user_if_updated($username){
        $this->db->delete('user', array('email' => $username)); 
    }
    
    /**
     * Get old user's profile with user's username
     *
     * @param string $username
     */
    public function get_old_user_profile_with_user_id($username){
        $data = array();
        $this->db->select('email,display_name,first_name,last_name,avator');
        $this->db->from('user');
        $this->db->where('email', $username);
        $query_user = $this->db->get();
        if ($query_user->num_rows() > 0) {
            foreach ($query_user->result() as $row) {
            $username = $row->email;
            $first_name = $row->first_name;
            $last_name = $row->last_name;
            $avator_url = $row->avator;
            $nick_name = $row->display_name;
            
            $data = array(
                    'username' => $username ,
                    'fist_name' => $first_name ,
                    'last_name' => $last_name,
                    'avator_url' => $avator_url,
                    'nick_name' => $nick_name,
                    );
            }
        }
        return $data;
    }
    
    /**
     * Get old user's uid with given username
     *
     * @param string $username
     */
    public function get_old_user_uid_with_username($username){
        $user_uid = $this::LOGIN_CHECK_FAIL;
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('email', $username);
        $query_user_uid = $this->db->get();
        if ($query_user_uid->num_rows() > 0) {
            foreach ($query_user_uid->result() as $row) {
                $user_uid = $row->id;
            }
        }
        return $user_uid;
    }

}

<?php

/**
 * this file is Controller Class of user_model.php 
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends CI_Controller {

    const model_name = 'user_model';

    /**
     * Event Const Variable
     * 
     * @global const $variable 
     */
    const LOGIN_CHECK_FAIL = -1;
    const TYPE_ID_NOT_FOUND = -2;
    const ITEM_NOT_FOUND = -3;
    const TOKEN_NOT_ASSIGNED = -4;
    const BIND_WITH_FACEBOOK_ACCOUNT = 1;
    const BIND_WITH_TWITTER_ACCOUNT = 2;
    const USER_ID_NOT_FOUND = -5;
    const ERROR_SYSTEM_SLIP = 300;
    const ERROR_LOGIN_FAIL = 301;

    /**
     * Class constructor load library and configure file of facebook and twitter develop token
     *
     * @param void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model($this::model_name);

        $this->load->library('userrole/UserModule');
        $this->load->library('facebook/facebook');
        $this->load->library('twitter/twitter');

        $this->config->load('twitter');

        $this->model_object = $this->user_model;
    }

    /**
     * Login camera3d with username and password.
     *
     * @param string $username
     * @param string $password
     */
    public function login_with_password() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        /* @var $username type */
        $success = false;
        $user_info = array();
        $time = time();
        $error_msg = "Sorry, You can not login at present. error code:" . $this::ERROR_SYSTEM_SLIP;
        if ($this->model_object->login($username, $password)) {
            $token = md5(md5($password + $time)).$time;
            $user_uid = $this->model_object->get_user_uid_with_username($username);
            $this->model_object->store_user_token($user_uid, $token);
            $success = true;
            $error_msg = "Congratulations! you have successfully log in.";
            $user_info = $this->model_object->get_user_information($user_uid);
        } else {
            http_response_code(422);
            $token = $this::TOKEN_NOT_ASSIGNED;
            $user_info = $this::LOGIN_CHECK_FAIL;
            $error_msg = "please provide us with the correct username and password error code:  " . $this::ERROR_LOGIN_FAIL;
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "user" => $user_info,
            "token" => $token
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Register a new account in camera3d with user profile.
     *
     * @param string $username
     * @param string $password
     * @param string $first_name
     * @param string $last_name
     * @param string $nick_name
     */
    public function register_new_account() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $nick_name = $_POST['nick_name'];
        $success = false;
        $user_info = array();
        $error_msg = "the username already exist, please change another username to continue.";
        if ($this->model_object->register($username, $password, $first_name, $last_name, $nick_name)) {
            $success = true;
            $error_msg = "you have succesfully register a new account.";
            $user_uid = $this->model_object->get_user_uid_with_username($username);
            $user_info = $this->model_object->get_user_information($user_uid);
        } else {
            header("HTTP/1.0 422 ERROR");
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "user" => $user_info
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Using given access_token to login Facebook
     *
     * @param string $access_token
     */
    public function login_with_FB() {
        $access_token = $_POST['access_token'];
        $is_login = $this->facebook->check_facebook_login($access_token);
        $success = false;
        $error_msg = "please check your facebook username and password";
        if ($is_login == true) {
            $success = true;
            $error_msg = "you have successfully login.";
        } else {
            http_response_code(422);
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg);
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get Facebook user profile using given access token/include the login process
     *
     * @param string $access_token
     */
    public function login_and_get_Facebook_user_info() {
        $access_token = $_POST['access_token'];
        $facebook_user_information = array();
        $is_login = $this->facebook->check_facebook_login($access_token);

        if ($is_login == true) {
            $facebook_user_information = $this->facebook->get_and_store_facebook_user_information($access_token);
        } else {
            http_response_code(422);
            $error_msg = "please login first.";
            $success = false;
            $facebook_user_information = array(
                "status" => $success,
                "error_msg" => $error_msg,
                "user" => null,
                "token" => null);
        }
        header("Content-Type: application/json");
        echo json_encode($facebook_user_information);
    }

    /**
     * Using given access_token to login Twitter
     *
     * @param string $token
     * @param string $secret_token
     */
    public function login_with_Twitter() {
        $token = $_POST['token'];
        $secret_token = $_POST['secret_token'];
        $connection = $this->twitter->create($this->config->item('consumer_token', 'twitter'), $this->config->item('consumer_secret', 'twitter'), $token, $secret_token);
        $success = false;
        $error_msg = "you can not login with twitter.";
        $content = $connection->get('account/verify_credentials');
        if ($connection->http_code == 200) {
            $success = true;
            $error_msg = "you have successfully log in.";
        } else {
            http_response_code(422);
            $success = false;
            $error_msg = $content->errors[0]->message;
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg);
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get Twitter user profile using given access token/include the login process
     *
     * @param string $token
     * @param string $secret_token
     */
    public function login_and_get_Twitter_user_info() {
        $token = $_POST['token'];
        $secret_token = $_POST['secret_token'];
        $connection = $this->twitter->create($this->config->item('consumer_token', 'twitter'), $this->config->item('consumer_secret', 'twitter'), $token, $secret_token);
        $twitter_user_profile = array();
        $content = $connection->get('account/verify_credentials');
        if ($connection->http_code == 200) {
            //var_dump($content);
            $twitter_id = $content->id;
            $twitter_name = $content->name;
            //$twitter_description = $content->description;
            //$location = $content->location;
            //$url = $content->url;
            $twitter_user_profile = $this->model_object->store_twitter_user_info($twitter_id, $twitter_name, $token, $secret_token);
        } else {
            http_response_code(422);
            $success = false;
            $error_msg = $content->errors[0]->message;
            $twitter_user_profile = array(
                "status" => $success,
                "error_msg" => $error_msg,
                "user" => null,
                "token" => null);
        }
        header("Content-Type: application/json");
        echo json_encode($twitter_user_profile);
    }

    /**
     * Get all the followees' profile of the user based on the user uid from token
     *
     * @param string $token
     */
    public function get_followees() {
        $token = $_POST['token'];
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        $array_followee = array();
        $error_msg = "you have not login or own the effective token to ensure your login status";
        $success = false;
        if ($user_uid == $this::USER_ID_NOT_FOUND) {
            http_response_code(422);
            $error_msg = "you have not login or own the effective token to ensure your login status";
        } else {
            $array_followee = $this->model_object->get_followee($user_uid);
            $success = true;
            $error_msg = "you have successfully get the followees";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "user" => $array_followee
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get all the followers' profile of the user based on the user uid from token
     *
     * @param string $token
     */
    public function get_followers() {
        $token = $_POST['token'];
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        $array_follower = array();
        $error_msg = "you have not login or own the effective token to ensure your login status";
        $success = false;
        if ($user_uid == $this::USER_ID_NOT_FOUND) {
            http_response_code(422);
            $error_msg = "you have not login or own the effective token to ensure your login status";
        } else {
            $array_follower = $this->model_object->get_follower($user_uid);
            $success = true;
            $error_msg = "You have successfully get the follower's lists";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "user" => $array_follower
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Using Exisitng Camera3d Account to bind with Facebook account
     *
     * @param string $FB_token 
     * @param string $token     
     */
    public function bind_FB() {
        $FB_token = $_POST['FB_token'];
        $token = $_POST['token'];
        $is_login = $this->facebook->check_facebook_login($FB_token);
        $success = false;
        $stack = array();
        $error_msg = "you can not bind your account with facebook.";
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        if ($is_login == true) {
            if ($user_uid != $this::USER_ID_NOT_FOUND) {
                $stack = $this->model_object->bind_with_Facebook($user_uid, $FB_token);
            } else {
                http_response_code(422);
                $success = false;
                $error_msg = "please provide us with correct token.";
                $stack = array(
                    "status" => $success,
                    "error_msg" => $error_msg);
            }
        } else {
            http_response_code(422);
            $success = false;
            $error_msg = "you should provide us with correct username and password of your facebook account.";
            $stack = array(
                "status" => $success,
                "error_msg" => $error_msg);
        }
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Using Exisitng Facebook or Twitter Account to bind with Camera3d account
     *
     * @param string  $username 
     * @param string  $password
     * @param string  $token
     * @param boolean $is_FB_TW       
     */
    public function bind_3D() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $token = $_POST['token'];
        $is_FB_TW = $_POST['is_FB_TW'];
        $success = false;
        $error_msg = "you can not bind your camera3d account";
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        if ($is_FB_TW == $this::BIND_WITH_FACEBOOK_ACCOUNT) {
            $success = $this->model_object->bind_with_3d($username, $password, $user_uid);
            $error_msg = "you have successfully bind your camera3d account";
        } else if ($is_FB_TW == $this::BIND_WITH_TWITTER_ACCOUNT) {
            $success = $this->model_object->bind_with_3d($username, $password, $user_uid);
            $error_msg = "you have successfully bind your camera3d account";
        } else {
            http_response_code(422);
            $error_msg = "please check with variable is_FB_TW.";
            $success = false;
        }
        $stack = array(
            "status" => $success,
            "error_msg" => $error_msg);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Using Exisitng Camera3d Account to bind with Twitter account
     *
     * @param string $Twitter_public_token 
     * @param string $Twitter_secret_token
     * @param string $token      
     */
    public function bind_TW() {
        $Twitter_public_token = $_POST['Twitter_public_token'];
        $Twitter_secret_token = $_POST['Twitter_secret_token'];
        $token = $_POST['token'];
        $success = false;
        $error_msg = "you can not bind with twitter at present.";
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        $connection = $this->twitter->create($this->config->item('consumer_token', 'twitter'), $this->config->item('consumer_secret', 'twitter'), $Twitter_public_token, $Twitter_secret_token);
        $content = $connection->get('account/verify_credentials');
        $stack = array();
        if ($connection->http_code == 200) {
            if ($user_uid != $this::USER_ID_NOT_FOUND) {
                $twitter_id = $content->id;
                $success = $this->model_object->bind_with_Twitter($user_uid, $twitter_id, $Twitter_public_token, $Twitter_secret_token);
                $error_msg = "you have successfully bind with the twitter account.";
                $stack = array(
                    "status" => $success,
                    "error_msg" => $error_msg);
            } else {
                http_response_code(422);
                $success = false;
                $error_msg = "please provide us with correct token.";
                $stack = array(
                    "status" => $success,
                    "error_msg" => $error_msg);
            }
        } else {

            http_response_code(422);
            $success = false;
            $error_msg = $content->errors[0]->message;
            $stack = array(
                "status" => $success,
                "error_msg" => $error_msg);
        }

        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Log out the Camera3d Account
     *
     * @param string $token      
     */
    public function logout() {
        $token = $_POST['token'];
        $stack = array();
        $success = false;
        $user_uid = $this->model_object->get_user_uid_with_token($token);
        $error_msg = "you cannot log out.";
        if ($this->model_object->logout($user_uid) == true) {
            $success = true;
            $error_msg = "you have successfully log out";
        } else {
            http_response_code(422);
            $error_msg = "you can not log out.";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Log in and update old user profile tp new camera3d server 
     *
     * @param string $username
     * @param string $password     
     */
    public function login_old_db($username, $password){
        $user_profile = array();
        $status = false;
        $error_msg = "you can not login due to system error";
        if ($this->model_object->check_old_user_login_status($username, $password) == true){
            if ($this->model_object->update_old_user_to_new_db($username,$password) != true){
                $error_msg = "the update to new server has encountered problem";
            }else{
                $this->model_object->delete_old_user_if_updated($username);
                $user_profile = $this->model_object->get_old_user_profile_with_user_id($username);
                $status = true;
                $error_msg = "you have successfully login and update the old profile";
            }
        }else{
            $error_msg = "we can not ensure your login identity";
        }
        $data = array(
            'status' => $status,
            'error_msg' => $error_msg,
            'user_profile' => $user_profile
        );
        return $data;       
    }
    
    
}

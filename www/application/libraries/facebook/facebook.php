<?php

/**
 * this file is Facebook Library
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Library Used 
 * 
 * @package facebook
 */
require_once( APPPATH . 'libraries/facebook/Facebook/GraphObject.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/GraphSessionInfo.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookSession.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/HttpClients/FacebookCurl.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookHttpable.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookResponse.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookSDKException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRequestException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookAuthorizationException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRequest.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRedirectLoginHelper.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/Entities/AccessToken.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/GraphUser.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookServerException.php' );

use Facebook\GraphSessionInfo;
use Facebook\FacebookSession;
use Facebook\FacebookCurl;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookResponse;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\FacebookSDKException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;
use Facebook\GraphUser;
use Facebook\FacebookServerException;

class Facebook {

    var $ci;
    var $helper;
    var $session;

    /**
     * Table Name Variable
     * 
     * @global const $variable 
     */
    var $table_token = "usr_login";
    var $table_user = "usr_user";
    var $table_friend = "usr_friend";
    var $table_facebook = "usr_facebook_user";

    const USER_ALREADY_EXIST = -6;

    public function __construct() {
        $this->ci = & get_instance();

        $this->ci->load->library('uidgenerator/UidGenerator');

        FacebookSession::setDefaultApplication($this->ci->config->item('api_id', 'facebook'), $this->ci->config->item('app_secret', 'facebook'));
        $this->helper = new FacebookRedirectLoginHelper($this->ci->config->item('redirect_url', 'facebook'));

        if ($this->ci->session->userdata('fb_token')) {
            $this->session = new FacebookSession($this->ci->session->userdata('fb_token'));

            // Validate the access_token to make sure it's still valid
            try {
                if (!$this->session->validate()) {
                    $this->session = false;
                }
            } catch (Exception $e) {
                // Catch any exceptions
                $this->session = false;
            }
        } else {
            try {
                $this->session = $this->helper->getSessionFromRedirect();
            } catch (FacebookRequestException $ex) {
                // When Facebook returns an error
            } catch (\Exception $ex) {
                // When validation fails or other local issues
            }
        }

        if ($this->session) {
            $this->ci->session->set_userdata('fb_token', $this->session->getToken());

            $this->session = new FacebookSession($this->session->getToken());
        }
    }

    public function get_login_url() {
        return $this->helper->getLoginUrl($this->ci->config->item('permissions', 'facebook'));
    }

    public function get_logout_url() {
        if ($this->session) {
            return $this->helper->getLogoutUrl($this->session, site_url('logout'));
        }
        return false;
    }

    /**
     * get facebook user information
     *
     * @param void  
     */
    public function get_user() {
        if ($this->session) {
            try {
                $request = (new FacebookRequest($this->session, 'GET', '/me'))->execute();
                $user = $request->getGraphObject()->asArray();

                return $user;
            } catch (FacebookRequestException $e) {
                return false;

                /* echo "Exception occured, code: " . $e->getCode();
                  echo " with message: " . $e->getMessage(); */
            }
        }
    }

    /**
     * use access_token to check whether you have login facebook or not
     *
     * @param string $access_token   
     */
    public function check_facebook_login($access_token) {
        $session = new FacebookSession($access_token);
        $error_msg = "You are now log off.";
        if ($session) {
            try {
                return true;
            } catch (FacebookRequestException $e) {
                $error_msg = "Exception has occured when tring to visit your account: " . $e->getCode() . "detailed: " . $e->getMessage();
                return $error_msg;
            }
        } else {
            return $error_msg;
        }
    }

    /**
     * Get Facebook user information by access token, not store any user information in database
     *
     * @param string $access_token   
     */
    public function get_Facebook_user_information($access_token) {
        $error_msg = "you can not get the user profile at present.";
        $success = false;
        $session = new FacebookSession($access_token);
        $user = null;
        $array_facebook_user = array();
        $token_val = null;
        if ($session) {
            try {
                // Already Logged in
                $success = true;
                $error_msg = " you have successfully get the user profile";
                $request = (new FacebookRequest($session, 'GET', '/me'))->execute();
                $user = $request->getGraphObject()->asArray();

                //$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
                $user_info = $request->getGraphObject();
                //$user_fb_gender = $user_info->getProperty("gender");
                $user_fb_first_name = $user_info->getProperty("first_name");
                $user_fb_last_name = $user_info->getProperty("last_name");
                $user_fb_id = $user_info->getProperty("id");
                //$user_fb_bio = $user_info->getProperty("bio");
                //$user_fb_link = $user_info->getProperty("link");
                //$user_fb_update_time = $user_info->getProperty("updated_time");
                //only get from advanced user, get further permission
                //$user_fb_email = $user_info->getProperty("email");

                $array_facebook_user = array(
                    "status" => $success,
                    "error_msg" => $error_msg,
                    "access_token" => $access_token,
                    "facebook_id" => $user_fb_id,
                    "token" => $token_val,
                    "fb_first_name" => $user_fb_first_name,
                    "fb_last_name" => $user_fb_last_name);
            } catch (FacebookRequestException $e) {
                $error_msg = "Exception has occured when tring to visit your account: "
                        . $e->getCode() . "detailed:" . $e->getMessage();
                $success = false;
                $array_facebook_user = array(
                    "status" => $success,
                    "error_msg" => $error_msg,
                );
            }
        }
        return $array_facebook_user;
    }

    /**
     * Get Facebook user information by access token, and store user information in database
     *
     * @param string $access_token   
     */
    public function get_and_store_facebook_user_information($access_token) {
        $error_msg = "you can not get the user profile at present.";
        $success = false;
        $time = time();
        $session = new FacebookSession($access_token);
        $user = null;      
        $array_facebook_user = array();
        $token_val = null;
        if ($session) {
            try {
                // Already Logged in
                $success = true;
                $error_msg = " you have successfully get the user profile";
                $request = (new FacebookRequest($session, 'GET', '/me'))->execute();
                $user = $request->getGraphObject()->asArray();

                //$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
                $user_info = $request->getGraphObject();
                //$user_fb_gender = $user_info->getProperty("gender");
                $user_fb_first_name = $user_info->getProperty("first_name");
                $user_fb_last_name = $user_info->getProperty("last_name");
                $user_fb_id = $user_info->getProperty("id");
                //$user_fb_bio = $user_info->getProperty("bio");
                //$user_fb_link = $user_info->getProperty("link");
                //$user_fb_update_time = $user_info->getProperty("updated_time");
                //only get from advanced user, get further permission
                //$user_fb_email = $user_info->getProperty("email");
                $user_new_id = 0;
                $is_facebook_id_duplicate = $this->ci->user_model->check_facebook_id_duplicate($user_fb_id);
                if ($is_facebook_id_duplicate == false) {
                    //store in our database
                    $new_uid_result = $this->ci->uidgenerator->request_uid($this->table_user);
                    $new_uid = $new_uid_result[0]->uid_gen_current_uid;
                    $user_new_id = $new_uid;
                    $data = array(
                        'user_uid' => $new_uid,
                        'user_first_name' => $user_fb_first_name,
                        'user_last_name' => $user_fb_last_name,
                        'user_nick_name' => $user_fb_last_name);

                    $this->ci->db->insert('usr_user', $data);

                    $new_uid_token_result = $this->ci->uidgenerator->request_uid($this->table_token);
                    $new_token_uid = $new_uid_token_result[0]->uid_gen_current_uid;
                    $token_val = md5(md5($user_fb_last_name + $time));
                    $data_token = array(
                        'login_uid' => $new_token_uid,
                        'login_token_data' => $token_val,
                        'login_token_expire_date' => null,
                        'user_uid' => $new_uid);

                    $this->ci->db->insert('usr_login', $data_token);


                    $new_uid_fb_result = $this->ci->uidgenerator->request_uid($this->table_facebook);
                    $new_fb_uid = $new_uid_fb_result[0]->uid_gen_current_uid;
                    $data_fb = array(
                        'facebook_uid' => $new_fb_uid,
                        'user_uid' => $new_uid,
                        'access_token' => $access_token,
                        'facebook_id' => $user_fb_id);

                    $this->ci->db->insert('usr_facebook_user', $data_fb);
                } else {
                    //$user_new_id = $this::USER_ALREADY_EXIST;
                    $data_fb_update_token = array(
                        'access_token' => $access_token);
                    $this->ci->db->where('facebook_id', $user_fb_id);
                    $this->ci->db->update('usr_facebook_user', $data_fb_update_token);

                    $this->ci->db->select('user_uid');
                    $this->ci->db->from('usr_facebook_user');
                    $this->ci->db->where('facebook_id', $user_fb_id);
                    $type_query = $this->ci->db->get();
                    if ($type_query->num_rows() > 0) {
                        foreach ($type_query->result() as $row) {
                            $user_new_id = $row->user_uid;
                        }
                    }
                    
                    $token_val = $this->ci->user_model->get_user_token_with_user_uid($user_new_id);
                        }

                $array_user = array(
                    "user_uid" => $user_new_id,
                    "user_first_name" => $user_fb_first_name,
                    "user_last_name" => $user_fb_last_name,
                    "user_nick_name" => $user_fb_last_name,
                    "user_login_id" => null,
                    "user_avater_url" => null);

                $array_facebook_user = array(
                    "status" => $success,
                    "error_msg" => $error_msg,
                    "user" => $array_user,
                    "token" => $token_val);
            } catch (FacebookRequestException $e) {
                $error_msg = "Exception has occured when tring to visit your account: "
                        . $e->getCode() . "detailed:" . $e->getMessage();
                $success = false;
                $array_facebook_user = array(
                    "success" => $success,
                    "error_msg" => $error_msg,
                );
            }
        }
        return $array_facebook_user;
    }

}

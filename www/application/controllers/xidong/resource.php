<?php

/**
 * this file is Controller Class of resource_model.php 
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Resource extends CI_Controller {

    const model_name = 'resource_model';
    const user_model_name = 'user_model';

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
    const ERROR_SYSTEM_SLIP = 300;
    const ERROR_LOGIN_FAIL = 301;
    const TYPE_VIDEO = "video";
    const TYPE_IMAGE = "image";
    const TYPE_THUMNAIL = "thumbnail";

    /**
     * Table Name Variable
     * 
     * @global const $variable 
     */
    var $table_item = "res_item";

    /**
     * Class constructor load library
     *
     * @param void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model($this::model_name);
        $this->load->model($this::user_model_name);

        $this->model_object = $this->resource_model;
        $this->load->library('userrole/UserModule');
        $this->load->library('uidgenerator/UidGenerator');
    }

    /**
     * Create the item and assign the item id before upload resources
     *
     * @param string $token
     * @param string $item_name
     * @param string $item_description   
     */
    public function create_item() {
        $token = $_POST['token'];
        $item_name = $_POST['item_name'];
        $item_description = $_POST['item_description'];
        $time = date("Y-m-d H:i:s");
        $success = false;
        $error_msg = "you have failed to create the item.";

        $user_uid = $this->user_model->get_user_uid_with_token($token);
        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $new_uid_result = $this->uidgenerator->request_uid($this->table_item);
            $new_uid = $new_uid_result[0]->uid_gen_current_uid;

            $array_item = array(
                'item_uid' => $new_uid,
                'item_create_date' => $time,
                'user_uid' => $user_uid,
                'item_name' => $item_name,
                'item_description' => $item_description);

            $this->db->insert('res_item', $array_item);
            $success = true;
            $error_msg = "you have successfully create the item id, now please upload.";
        } else {
            http_response_code(422);
            $error_msg = "please login first before use.";
            $success = false;
            $array_item = null;
            $new_uid = null;
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "item_name" => $item_name,
            "content_create_date" => $time,
            "item_id" => $new_uid
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * After create the item upload the resources according to given item id
     *
     * @param string $token
     * @param int    $item_id
     * @param string $resource_type 
     * @param int    $resource_index 
     * @param string $resource_data  
     */
    public function add_resource() {
        $base_url = 'http://54.251.251.94/camera3d/www/upload_resources/';
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $resource_type = $_POST['resource_type'];
        $resource_index = $_POST['resource_index'];
        $user_uid = $this->user_model->get_user_uid_with_token($token);
        $error_msg = "you have failed to add resource due to internet not stable.";
        $success = false;
        $time = $item_uid.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
        $image_location = $_SERVER['DOCUMENT_ROOT'] . '/' . '/camera3d/www/' . 'upload_resources' . '/';
        $image_name = $time . '_' . $item_uid . $resource_index . '.jpg';
        $return_url = $base_url . $image_name;
        if ($resource_type == $this::TYPE_VIDEO) {
            $image_location = $_SERVER['DOCUMENT_ROOT'] . '/' . '/camera3d/www/' . 'upload_resources' . '/';
            $image_name = $time . '_' . $item_uid . $resource_index . '.mp4';
            $return_url = $base_url . $image_name;
        } else if ($resource_type == $this::TYPE_IMAGE) {
            $image_location = $_SERVER['DOCUMENT_ROOT'] . '/' . '/camera3d/www/' . 'upload_resources' . '/';
            $image_name = $time . '_' . $item_uid . $resource_index . '.jpg';
            $return_url = $base_url . $image_name;
        } else if ($resource_type == $this::TYPE_THUMBNAIL) {
            $image_location = $_SERVER['DOCUMENT_ROOT'] . '/' . '/camera3d/www/' . 'upload_resources' . '/';
            $image_name = $time . '_' . $item_uid . $resource_index . '.jpg';
            $return_url = $base_url . $image_name;
            $data_url = array(
                'user_avater_url' => $return_url);
            $this->db->where('user_uid', $user_uid);
            $this->db->update('usr_user', $data_url);          
        }

        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $tmp2 = $image_location . $image_name;
            $target_path = str_replace('//', '/', $tmp2);
            if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
                $type_uid = $this->model_object->get_type_uid_with_type_name($resource_type);
                $this->model_object->item_add($item_uid, $type_uid, $resource_index, $return_url);
                $error_msg = "you have successfully uploaded all resources.";
                $success = true;
            }
            $array_resource = array(
                "resource_type" => $resource_type,
                "resource_name" => $image_name,
                "resource_url" => $return_url,
                "resource_index" => $resource_index);
        } else {
            http_response_code(422);
            $array_resource = null;
            $error_msg = "you have failed to add resource because we can ensure your login status.";
            $success = false;
            $item_uid = null;
        }

        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "item_uid" => $item_uid,
            "resource" => $array_resource
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get all the feeds and data that user can have rights to get based on given user uid
     *
     * @param string $token
     * @param int    $index
     * @param string $batch_size  
     */
    public function get_feeds() {
        $token = $_POST['token'];
        $index = $_POST['index'];
        $batch_size = $_POST['batch_size'];        
        $success = false;
        $error_msg = "the system fails to get feeds.";
        $array_feed = array();
        $user_uid = $this->user_model->get_user_uid_with_token($token);

        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $error_msg = "you have successfully get the feed.";
            $array_feed = $this->model_object->get_dib_item_with_user_uid($user_uid, $index, $batch_size);
        } else {
            http_response_code(422);
            $array_feed = null;
            $success = false;
            $error_msg = "please login first.";
        }

        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "feed" => $array_feed);
       
        header("Content-Type: application/json");
        echo json_encode($data);
    }
    
    /**
     * Only work for loading test
     *
     * @param string $token
     * @param int    $item_uid
     * @param int    $index
     * @param string $batch_size  
     */
    public function get_simple_feeds(){
        $item_uid = $_REQUEST['item_uid'];
        $simple_feed = $this->model_object->get_simple_feeds($item_uid);
        $feed = array("item" => $simple_feed);
        header("Content-Type: application/json");
        echo json_encode($feed);
    }

    /**
     * Get all the detailed feeds and data that user can have rights to get based on given user uid
     *
     * @param string $token
     * @param int    $item_uid
     * @param int    $index
     * @param string $batch_size  
     */
    public function get_feeds_detail() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $success = false;
        $error_msg = "the system fails to get feeds.";
        $array_feed = array();
        $user_uid = $this->user_model->get_user_uid_with_token($token);

        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $error_msg = "you have successfully get the feed.";
            $array_feed = $this->model_object->get_dib_detailed_item_with_user_uid($item_uid, $user_uid);
        } else {
            http_response_code(422);
            $array_feed = null;
            $success = false;
            $error_msg = "please login first.";
        }

        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "feed" => $array_feed
        );
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get user's comments according to user uid and item uid
     *
     * @param string $token
     * @param int    $item_uid
     * @param int    $index
     * @param string $batch_size  
     */
    public function get_comments() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $index = $_POST['index'];
        $batch_size = $_POST['batch_size'];
        $stack = array();
        $success = false;
        $error_msg = "you can not get comments due to system error.";
        $array_comments = array();
        $user_uid = $this->user_model->get_user_uid_with_token($token);

        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $error_msg = "you have successfully get the comment.";
            $array_comments = $this->model_object->get_comment_with_item_uid($item_uid, $index, $batch_size);
        } else {
            http_response_code(422);
            $success = false;
            $array_comments = null;
            $error_msg = "please log in first before use.";
        }

        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "comments" => $array_comments
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Create comments under specific user uid and item uid
     *
     * @param string $token
     * @param int    $item_uid
     * @param int    $content
     */
    public function create_comments() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $content = $_POST['content'];
        $success = false;
        $stack = array();
        $time = date("Y-m-d H:i:s");
        $array_user = array();
        $error_msg = "you can not create comments due to system error.";
        $user_uid = $this->user_model->get_user_uid_with_token($token);
        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $error_msg = "you have successfully create the comment.";
            $array_user = $this->user_model->get_user_information($user_uid);
            $array_created_comment = array(
                "content_uid" => $this->model_object->store_created_comment($content, $item_uid, $user_uid),
                "content_create_date" => $time);
            $array_comment = array(
                "item_uid" => $item_uid,
                "user" => $array_user,
                "comment" => $array_created_comment);
        } else {
            http_response_code(422);
            $array_comment = null;
            $success = false;
            $error_msg = "you can not create comments, because you did not login.";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "comments" => $array_comment
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Create likes under specific user uid and item uid
     *
     * @param string $token
     * @param int    $item_uid
     */
    public function create_like() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $success = false;
        $time = date("Y-m-d H:i:s");
        $array_user = array();
        $error_msg = "you can not create likes due to system error.";
        $user_uid = $this->user_model->get_user_uid_with_token($token);
        $is_duplicate_user = $this->model_object->check_like_user_duplicate($user_uid, $item_uid);
        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            if ($is_duplicate_user == false) {
                $success = true;
                $error_msg = "you have successfully create the likes.";
                $array_user = $this->user_model->get_user_information($user_uid);
                $array_created_like = array(
                    "like_uid" => $this->model_object->store_created_like($item_uid, $user_uid),
                    "content_create_date" => $time);
                $array_like = array(
                    "user" => $array_user,
                    "like" => $array_created_like);
            } else {
                $success = false;
                $error_msg = "each one person can have only one like opportunity.";
                $array_like = null;
            }
        } else {
            http_response_code(422);
            $array_like = null;
            $success = false;
            $error_msg = "you can not create likes, because you do not login.";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "likes" => $array_like);
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Get item profile based on user uid and item uid
     *
     * @param string $token
     * @param int    $item_uid
     */
    public function get_item() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $success = false;
        $array_item = array();
        $stack = array();
        $error_msg = "you can not get item due to the system error";
        $user_uid = $this->user_model->get_user_uid_with_token($token);
        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $error_msg = "you have successfully get the items.";
            $array_item = $this->model_object->get_item_with_item_id($item_uid);
        } else {
            http_response_code(422);
            $success = false;
            $error_msg = "please check login status before get item.";
            $array_item = null;
        }

        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "item" => $array_item
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Return the minimum required version and the current version
     *
     * @param void
     */
    public function version_control() {
        $stack = array();
        $success = false;
        $error_msg = "you can not get the version detail due to system error";
        $stack_version = $this->model_object->version_detect();
        $success = true;
        $error_msg = "you have successfully get the version.";
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "version" => $stack_version
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Get the like number according to item uid
     *
     * @param string $token
     * @param int    $item_uid 
     */
    public function get_item_like_number() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $success = true;
        $stack = array();
        $error_msg = "you can not get the item like number.";
        $user_uid = $this->user_model->get_user_uid_with_token($token);

        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $like_number = $this->model_object->get_item_like_number($item_uid);
            $error_msg = "you have successfully get the like number.";
        } else {
            http_response_code(422);
            $success = false;
            $error_msg = "please login first.";
            $like_number = null;
        }
        $data = array(
            "status" => $success,
            "item_uid" => $item_uid,
            "like number" => $like_number,
            "error_msg" => $error_msg
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

    /**
     * Get the Comment number according to item uid
     *
     * @param string $token
     * @param int    $item_uid 
     */
    public function get_item_comment_number() {
        $token = $_POST['token'];
        $item_uid = $_POST['item_uid'];
        $success = false;
        $stack = array();
        $user_uid = $this->user_model->get_user_uid_with_token($token);
        $error_msg = "you can not get the item comment number";
        if ($user_uid != $this::USER_ID_NOT_FOUND) {
            $success = true;
            $comment_number = $this->model_object->get_item_comment_number($item_uid);
            $error_msg = "you have successfully get the item comment number.";
        } else {
            http_response_code(422);
            $success = false;
            $comment_number = null;
            $error_msg = "please login first.";
        }
        $data = array(
            "status" => $success,
            "error_msg" => $error_msg,
            "item_uid" => $item_uid,
            "comment number" => $comment_number
        );
        array_push($stack, $data);
        header("Content-Type: application/json");
        echo json_encode($stack);
    }

}

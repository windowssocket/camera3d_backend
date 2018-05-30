<?php

/**
 * this file is Model Class of resource.php 
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Resource_model extends CI_Model {

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

    /**
     * Table Name Variable
     * 
     * @global const $variable 
     */
    var $table_token = "usr_login";
    var $table_user = "usr_user";
    var $table_friend = "usr_friend";
    var $table_resource = "res_resource";
    var $table_like = "res_like";
    var $table_comment = "res_comment";
    var $table_type = "res_type";
    var $table_item = "res_item";

    /**
     * Class constructor load library
     *
     * @param void
     */
    function __construct() {
        parent::__construct();
        $this->load->library('userrole/UserModule');
        $this->load->library('uidgenerator/UidGenerator');
    }

    /**
     * Add one resource profile according to given item id
     *
     * @param int    $item_id
     * @param int    $type_uid
     * @param string $resource_index
     * @param string $resource_url  
     */
    public function item_add($item_id, $type_uid, $resource_index, $resource_url) {
        $new_uid_result = $this->uidgenerator->request_uid($this->table_resource);
        $new_uid = $new_uid_result[0]->uid_gen_current_uid;
        $data = array(
            'resource_uid' => $new_uid,
            'item_uid' => $item_id,
            'type_uid' => $type_uid,
            'resource_index' => $resource_index,
            'resource_url' => $resource_url);
        $this->db->insert('res_resource', $data);
        $item_success = true;
        return $item_success;
    }
    
    
    public function get_simple_feeds($item_uid){
        $id = 0;
        $this->db->select('item_name, item_description, item_create_date, user_uid');
        $this->db->from('res_item');
        $query_item = $this->db->get();
        $data_return = array();
        if ($query_item->num_rows() > 0) {
            foreach ($query_item->result() as $row) {
                $user_uid = $row->user_uid;
                $user_avater_url = $this->user_model->find_avater_url_with_user_uid($user_uid);
                $id = $id + 1;
                $url = $this->model_object->find_resource_url($item_uid);
                
                
                $data = array(
                    'id' => $id,
                    'name' => $row->item_name,
                    'status' => $row->item_description,
                    'timeStamp' => $row->item_create_date,
                    "profilePic" => $user_avater_url,
                    "image" => $url,
                    "like" => 1,
                    "url" => $url);
                array_push($data_return,$data);
                array_push($data_return,$data);
                array_push($data_return,$data);
            }
        }
        return $data_return;
    }
    
    public function find_resource_url($item_uid){
        $url = "default";
                $this->db->select('resource_url');
                $this->db->from('res_resource');
                $this->db->where('item_uid', $item_uid);
                $query_resource = $this->db->get();
                foreach ($query_resource->result() as $row) {
                    $url = $row->resource_url;    
                }
                return $url;
    }

    /**
     * Check whether this user has make duplicate likes to the same item.
     *
     * @param int  $user_uid
     * @param int  $item_uid 
     */
    public function check_like_user_duplicate($user_uid, $item_uid) {
        $is_duplicate = false;
        $query_is_duplicate = $this->db->get_where('res_like', array(
            'user_uid' => $user_uid,
            'item_uid' => $item_uid));
        if ($query_is_duplicate->num_rows() > 0) {
            $is_duplicate = true;
        }
        return $is_duplicate;
    }

    /**
     * Find the minimum required version and current
     *
     * @param void
     */
    public function version_detect() {
        $data = array();
        $this->db->select('version_stable,version_test');
        $this->db->from('ver_version');
        $query_version = $this->db->get();
        if ($query_version->num_rows() > 0) {
            foreach ($query_version->result() as $row) {
                $data = array(
                    'version_stable' => $row->version_stable,
                    'version_test' => $row->version_test);
            }
        }
        return $data;
    }

    /**
     * Get resource's type uid with its name
     *
     * @param string $type_name
     */
    public function get_type_uid_with_type_name($type_name) {
        $type_uid = $this::TYPE_ID_NOT_FOUND;
        $this->db->select('type_uid');
        $this->db->from('res_type');
        $this->db->where('type_name', $type_name);
        $type_query = $this->db->get();
        if ($type_query->num_rows() > 0) {
            foreach ($type_query->result() as $row) {
                $type_uid = $row->type_uid;
            }
        }
        return $type_uid;
    }

    /**
     * Get the user's item profile according to given user uid
     *
     * @param int $user_uid  
     */
    public function get_item_with_user_uid($user_uid) {
        $array_item = array();
        $array_item_feed = array();
        $item_uid = $this::ITEM_NOT_FOUND;
        $this->db->select('item_uid,item_create_date,item_name,item_description');
        $this->db->from('res_item');
        $this->db->where('user_uid', $user_uid);
        $query_user_item = $this->db->get();
        if ($query_user_item->num_rows() > 0) {
            foreach ($query_user_item->result() as $row) {
                $item_uid = $row->item_uid;
                $data = array(
                    "item_uid" => $row->item_uid,
                    "item_create_date" => $row->item_create_date,
                    "item_name" => $row->item_name,
                    "item_description" => $row->item_description,
                    "user" => $this->user_model->get_user_information($user_uid),
                    "resource" => $this->get_resource_with_item_uid($item_uid));
                $array_item = array(
                    "item" => $data,
                    "like_count" => $this->get_item_like_number($item_uid),
                    "comment_count" => $this->get_item_comment_number($item_uid),
                    "share_count" => 0);
                array_push($array_item_feed, $array_item);
            }
            
        }
        return $array_item_feed;
    }

    /**
     * Get the user's detailed item profile according to given user uid
     *
     * @param int $user_uid  
     */
    public function get_detailed_item_with_user_uid($user_uid) { 
        $array_item_resource = array();
        $array_item = array();
        $item_uid = $this::ITEM_NOT_FOUND;
        $this->db->select('item_uid,item_create_date,item_name,item_description');
        $this->db->from('res_item');
        $this->db->where('user_uid', $user_uid);
        $query_user_item = $this->db->get();
        if ($query_user_item->num_rows() > 0) {
            foreach ($query_user_item->result() as $row) {
                $item_uid = $row->item_uid;
                $data = array(
                    "item_uid" => $item_uid,
                    "item_create_date" => $row->item_create_date,
                    "item_name" => $row->item_name,
                    "item_description" => $row->item_description,
                    "resource" => $this->get_resource_with_item_uid($item_uid),
                    "user" => $this->user_model->get_user_information($user_uid));
                $array_item = array(
                    "item" => $data,
                    "like_count" => $this->get_item_like_number($item_uid),
                    "comment_count" => $this->get_item_comment_number($item_uid),
                    "share_count" => 0);
                array_push($array_item_resource, $array_item);
            }
        }
        return $array_item_resource;
    }

    /**
     * Get the resources info according to given item uid
     *
     * @param int $item_uid  
     */
    public function get_resource_with_item_uid($item_uid) {
        $query_resource = $this->db->get_where('res_resource', array('item_uid' => $item_uid));
        $array_resource = $query_resource->result();
        return $array_resource;
    }

    /**
     * Get the item profile that User has rights to get according to given user uid
     *
     * @param int $user_uid  
     */
    public function get_dib_item_with_user_uid($user_uid, $index, $batch_size) {
        $array_dib_item = array();
        $this->db->select('usr_friend.user_uid, usr_user.user_uid');
        $this->db->from('usr_user');
        $this->db->join('usr_friend', 'usr_friend.user_uid = usr_user.user_uid', 'left');
        $this->db->join('res_item', 'res_item.user_uid = usr_user.user_uid', 'left');
        $this->db->where('usr_user.user_uid', $user_uid);
        $this->db->or_where('usr_friend.following_user_uid', $user_uid);
        $this->db->limit($batch_size, $index);
        $this->db->order_by("res_item.item_create_date", "desc");
        $query_dib_user_item = $this->db->get();
        if ($query_dib_user_item->num_rows() > 0) {
            foreach ($query_dib_user_item->result() as $row) {
                $dib_user_uid = $row->user_uid;
                $array_dib_item = $this->get_item_with_user_uid($dib_user_uid);
            }
        }
        return array_slice($array_dib_item, $index, $batch_size);
    }

    /**
     * Get the detailed item profile that User has rights to get according to given user uid
     * @deprecated since version 1.0
     * @param int $user_uid  
     */
    public function get_dib_detailed_item_with_user_uid($item_uid, $user_uid) {
        $array_dib_detailed_item = array();
        $this->db->select('usr_friend.user_uid, usr_user.user_uid');
        $this->db->from('usr_user');
        $this->db->join('usr_friend', 'usr_friend.user_uid = usr_user.user_uid', 'left');
        $this->db->join('res_item', 'res_item.user_uid = usr_user.user_uid', 'left');
        $this->db->where('usr_user.user_uid', $user_uid);
        $this->db->or_where('usr_friend.following_user_uid', $user_uid);
        $this->db->order_by("res_item.item_create_date", "desc");
        $query_dib_user_detailed_item = $this->db->get();
        if ($query_dib_user_detailed_item->num_rows() > 0) {
            foreach ($query_dib_user_detailed_item->result() as $row) {
                $dib_user_detailed_uid = $row->user_uid;
                $array_dib_detailed_item = $this->get_detailed_resources_with_item_uid($item_uid, $dib_user_detailed_uid);
            }
        }
        return $array_dib_detailed_item;
    }

    /**
     * Get the detailed resources profile according to given item uid
     *
     * @param int $item_uid  
     */
    public function get_detailed_resources_with_item_uid($item_uid, $user_uid) {
        $array_detailed_feed = array();
        $this->db->select('item_create_date,item_name,item_description');
        $this->db->from('res_item');
        $this->db->where('item_uid', $item_uid);
        $query_item = $this->db->get();
        if ($query_item->num_rows() > 0) {
            foreach ($query_item->result() as $row) {
            $data = array(
                "item_uid" => $item_uid,
                "item_create_date" => $row->item_create_date,
                "item_name" => $row->item_name,
                "item_description" => $row->item_description,
                "resource" => $this->get_resource_with_item_uid($item_uid),
                "user" => $this->user_model->get_user_information($user_uid));
            }
             $array_item = array(
                    "item" => $data,
                    "like_count" => $this->get_item_like_number($item_uid),
                    "comment_count" => $this->get_item_comment_number($item_uid),
                    "share_count" => 0,
                    "comments" => $this->get_comment_with_item_uid($item_uid, 0, 20));
            }
        return $array_item;
    }

    /**
     * Store new comment content with item id and user uid
     *
     * @param string $content  
     * @param int    $item_uid
     * @param int    $user_uid 
     */
    public function store_created_comment($content, $item_uid, $user_uid) {
        $new_uid_result = $this->uidgenerator->request_uid($this->table_comment);
        $new_uid = $new_uid_result[0]->uid_gen_current_uid;
        $time = time();
        $data = array(
            'comment_uid' => $new_uid,
            'user_uid' => $user_uid,
            'item_uid' => $item_uid,
            'comment_create_date' => $time,
            'comment_content' => $content);

        $this->db->insert('res_comment', $data);
        return $new_uid;
    }

    /**
     * Store new like with item uid and user uid
     *
     * @param int $item_uid  
     * @param int $user_uid
     */
    public function store_created_like($item_uid, $user_uid) {
        $new_uid_result = $this->uidgenerator->request_uid($this->table_like);
        $new_uid = $new_uid_result[0]->uid_gen_current_uid;
        $data = array(
            'like_uid' => $new_uid,
            'user_uid' => $user_uid,
            'item_uid' => $item_uid);

        $this->db->insert('res_like', $data);
        return $new_uid;
    }

    /**
     * Get item profile according to item uid
     *
     * @param int $item_uid  
     */
    public function get_item_with_item_id($item_uid) {
        $user_uid = $this::USER_ID_NOT_FOUND;
        $array_item_user = array();
        $data = array(
            'item_like_number' => $this->get_item_like_number($item_uid),
            'item_comment_number' => $this->get_item_comment_number($item_uid));
        $this->db->where('item_uid', $item_uid);
        $this->db->update('res_item', $data);

        $this->db->select('user_uid, item_uid,item_create_date,item_name,item_description');
        $this->db->from('res_item');
        $this->db->where('item_uid', $item_uid);      
        $user_item = $this->db->get();
        if ($user_item->num_rows() > 0) {
            foreach ($user_item->result() as $row) {
                $user_uid = $row->user_uid;
                $data_item = array(
                    "item_uid" => $item_uid,
                    "item_create_date" => $row->item_create_date,
                    "item_name" => $row->item_name,
                    "item_description" => $row->item_description,
                    "user" => $this->user_model->get_user_information($user_uid),
                    "resources" => $this->get_resource_with_item_uid($item_uid));
                $array_item = array(
                    "items" => $data_item,
                    "like_count" => $this->get_item_like_number($item_uid),
                    "comment_count" => $this->get_item_comment_number($item_uid),
                    "share_count" => 0);
                 array_push($array_item_user, $array_item);
            }
        }
        return $array_item_user;
    }

    /**
     * Get this user's comment content
     * @deprecated since version 1.0
     * @param int $user_uid  
     */
    public function get_comment($user_uid) {
        $query_comment = $this->db->get_where('res_comment', array('user_uid' => $user_uid));
        $comment = $query_comment->result_array();
        return $comment;
    }

    /**
     * Get this item's comment content
     * 
     * @param int $item_uid 
     */
    public function get_comment_with_item_uid($item_uid, $index, $batch_size){
        $user_uid = $this::USER_ID_NOT_FOUND;
        $array_comment_item = array();
        $this->db->select('user_uid, comment_uid,comment_create_date,comment_content');
        $this->db->from('res_comment');
        $this->db->where('item_uid', $item_uid);
        $this->db->limit($batch_size, $index);
        $query_comment = $this->db->get();
        if ($query_comment->num_rows() > 0) {
           foreach ($query_comment->result() as $row) {
               $user_uid = $row->user_uid;
               $data = array(
                    "item_uid" => $item_uid,
                    "comment_uid" => $row->comment_uid,
                    "comment_create_date" => $row->comment_create_date,
                    "comment_content" => $row->comment_content,
                    "user" => $this->user_model->get_user_information($user_uid));
               $array_comment = array(
                    "comment" => $data,
                    "comment_count" => $this->get_item_comment_number($item_uid));
          array_push($array_comment_item, $array_comment);
            }
        }
        return $array_comment_item;
    }
    /**
     * Count the like number based on item uid
     *
     * @param int $item_uid 
     */
    public function get_item_like_number($item_uid) {
        $query_like_number = $this->db->get_where('res_like', array('item_uid' => $item_uid));
        $like_number = $query_like_number->num_rows();
        return $like_number;
    }

    /**
     * Count the comment number based on item uid
     *
     * @param int $item_uid 
     */
    public function get_item_comment_number($item_uid) {
        $query_comment_number = $this->db->get_where('res_comment', array('item_uid' => $item_uid));
        $comment_number = $query_comment_number->num_rows();
        return $comment_number;
    }
    
    /**
     * get old image use old method
     * @deprecated since version 1.0
     * @param string $username 
     */
    public function get_old_item_in_order($username){
        $this->db->select('id,frame,title,description,create_date');
        $this->db->from('feed_set');
        $this->db->where('email', $username);
        $query_item = $this->db->get();
        if ($query_item->num_rows() > 0) {
            foreach ($query_item->result() as $row) {
            $data = array(
                "image_name" => $row->id,
                "item_size" => $row->frame,
                "item_title" => $row->title,
                "item_description" => $row->description,
                "item_create_date" => $row->create_date);
            }
             $array_item = array(
                    "item" => $data,
                    "user" => $this->user_model->get_old_user_profile_with_user_id($username)
                );
            }
        return $array_item;
    }
    
    /**
     * get old image use old method
     * @deprecated since version 1.0
     * @param string $user_uid 
     */
    
    public function update_old_item_with_new_db($user_uid){
        $data = array();
        $base_url = 'https://apptechhk-camera3d.s3.amazonaws.com';
        $this->db->select('id,size,create_date,description,like,comment');
        $this->db->from('feed_set');
        $this->db->where('user_id', $user_uid);
        $query_user_item = $this->db->get();
        $new_uid_result = $this->uidgenerator->request_uid("feed_set");
        $new_uid = $new_uid_result[0]->uid_gen_current_uid;
        if ($query_user_item->num_rows() > 0) {
            foreach ($query_user_item->result() as $row) {
                $item_uid = $new_uid;
                $size = $row->size;
                $item_create_date = $row->create_date;
                $user_new_uid = $row->user_uid;
                $item_name = $row->title;
                $item_description = $row->description;
                $item_like_number = $row->like;
                $item_comment_number = $row->comment;
                for($i = 0; $i < $size; $i++){
                    $resource_url = $base_url."/".$id."_".$i.".jpg";
                    $resource_index = $i;
                    $type_uid = 1001;
                    $item_uid = $new_uid;
                    $resource_uid = $resource_uid;
                }
            }
        }
    }
    
    /**
     * Delete old item if already updated
     * @deprecated since version 1.0
     * @param int $user_uid 
     */
    public function delete_old_item_if_updated($user_uid){
        $this->db->delete('feed_set', array('user_uid' => $user_uid)); 
    }

}

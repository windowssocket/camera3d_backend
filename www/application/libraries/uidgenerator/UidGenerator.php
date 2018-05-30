<?php

/**
 * this file is UID generator Library
 * 
 * @author Chen, Xidong <chenxidong2013@gmail.com>
 * @copyright (c) 2014, Chen, Xidong
 * @version 1.0
 */

class UidGenerator extends CI_Controller {
    
    var $ci = null;

    public function __construct() {
        $this->ci = & get_instance();
    }
    
    /**
     * Assign Table UID according to specific table name
     *
     * @param string $table_name
     */
    function request_uid($table_name){
        //Must inside a transaction
        $this->ci->db->trans_start();
        $this->ci->db->query("UPDATE sys_uid_gen set uid_gen_current_uid=uid_gen_current_uid+1 WHERE uid_gen_table_name='$table_name'");
        $query = $this->ci->db->get_where('sys_uid_gen', array('uid_gen_table_name' => $table_name));
        $this->ci->db->trans_complete();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
}


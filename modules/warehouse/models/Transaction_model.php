<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model {
    protected $table = 'transactions';
    public function __construct(){ parent::__construct(); }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_recent($limit = 50)
    {
        return $this->db->order_by('created_at','desc')->limit($limit)->get($this->table)->result();
    }
}

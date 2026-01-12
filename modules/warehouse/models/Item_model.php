<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model {
    protected $table = 'items';
    public function __construct(){ parent::__construct(); }

    public function get_all()
    {
        return $this->db->order_by('id','desc')->get($this->table)->result();
    }

    public function get($id)
    {
        return $this->db->where('id',$id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

    public function update_qty($id, $delta)
    {
        // $delta bisa positif (inbound) atau negatif (outbound)
        $this->db->set('qty', 'qty + ('.(int)$delta.')', FALSE);
        $this->db->where('id', $id);
        $this->db->update($this->table);
    }
}

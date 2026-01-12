<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('warehouse/item_model');
        $this->load->model('warehouse/transaction_model');
        $this->load->helper(['url','form']);
        $this->load->library('form_validation');
    }

    public function scan()
    {
        $this->load->view('outbound/scan');
    }

    public function process()
    {
        $payload = $this->input->post('payload'); // ITEM:{id}
        $qty = (int)$this->input->post('qty');

        if (strpos($payload, 'ITEM:') !== 0) {
            echo json_encode(['status'=>false,'message'=>'Payload tidak valid']); return;
        }
        $id = (int)substr($payload,5);
        $item = $this->item_model->get($id);
        if (!$item) {
            echo json_encode(['status'=>false,'message'=>'Item tidak ditemukan']); return;
        }

        if ($item->qty < $qty) {
            echo json_encode(['status'=>false,'message'=>'Stok tidak cukup','current_qty'=>$item->qty]); return;
        }

        $this->transaction_model->insert([
            'type' => 'outbound',
            'item_id' => $id,
            'qty' => $qty,
            'barcode_data' => $payload
        ]);
        $this->item_model->update_qty($id, -$qty);

        echo json_encode(['status'=>true,'message'=>'Outbound sukses','item'=> $item->name, 'new_qty' => $item->qty - $qty]);
    }
}

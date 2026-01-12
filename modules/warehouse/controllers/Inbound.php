<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound extends MY_Controller {
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
        // view yang memuat html5-qrcode scanner
        $this->load->view('inbound/scan');
    }

    public function process()
    {
        // endpoint dipanggil dari form setelah scan atau manual input
        $payload = $this->input->post('payload'); // eks: ITEM:12
        $qty = (int)$this->input->post('qty');

        if (strpos($payload, 'ITEM:') !== 0) {
            echo json_encode(['status'=>false,'message'=>'Payload tidak valid']); return;
        }
        $id = (int)substr($payload,5);
        $item = $this->item_model->get($id);
        if (!$item) {
            echo json_encode(['status'=>false,'message'=>'Item tidak ditemukan']); return;
        }

        // simpan transaksi dan update qty
        $this->transaction_model->insert([
            'type' => 'inbound',
            'item_id' => $id,
            'qty' => $qty,
            'barcode_data' => $payload
        ]);
        $this->item_model->update_qty($id, $qty);

        echo json_encode(['status'=>true,'message'=>'Inbound sukses','item'=> $item->name, 'new_qty' => $item->qty + $qty]);
    }
}

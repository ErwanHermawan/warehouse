<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('warehouse/item_model');
        $this->load->helper(['url','form','html']);
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['items'] = $this->item_model->get_all();
        $this->load->view('items/index', $data);
    }

    public function create()
    {
        $this->form_validation->set_rules('sku','SKU','required|is_unique[items.sku]');
        $this->form_validation->set_rules('name','Nama','required');
        $this->form_validation->set_rules('qty','Qty','required|integer');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('items/form');
            return;
        }

        $post = $this->input->post();
        $id = $this->item_model->insert([
            'sku' => $post['sku'],
            'name' => $post['name'],
            'qty' => (int)$post['qty']
        ]);

        redirect('items');
    }

    public function view($id = NULL)
    {
        $item = $this->item_model->get($id);
        if (!$item) show_404();
        $data['item'] = $item;
        $this->load->view('items/view', $data);
    }

    // Menghasilkan URL QR (meng-encode string yang bisa diproses saat di-scan)
    public function qr($id = NULL)
    {
        $item = $this->item_model->get($id);
        if (!$item) show_404();

        // data yang di-embed ke QR: ITEM:{id}
        $payload = 'ITEM:'.$item->id;
        // gunakan Google Charts API untuk demo:
        $qr_url = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.urlencode($payload);

        $data['qr_url'] = $qr_url;
        $data['item'] = $item;
        $this->load->view('items/qr', $data);
    }
}

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html><html><head><meta charset="utf-8"><title>Tambah Item</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head><body class="p-4">
<div class="container">
    <h1>Tambah Item</h1>
    <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
    <?php echo form_open('items/create'); ?>
    <div class="form-group">
        <label>SKU</label>
        <input type="text" name="sku" class="form-control" value="<?php echo set_value('sku'); ?>">
    </div>
    <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" value="<?php echo set_value('name'); ?>">
    </div>
    <div class="form-group">
        <label>Qty</label>
        <input type="number" name="qty" class="form-control" value="<?php echo set_value('qty',0); ?>">
    </div>
    <button class="btn btn-primary">Simpan</button>
    <?php echo form_close(); ?>
</div>
</body></html>

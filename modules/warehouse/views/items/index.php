<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html><html><head><meta charset="utf-8"><title>Items</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head><body class="p-4">
<div class="container">
    <h1>Items</h1>
    <a class="btn btn-primary mb-2" href="<?php echo site_url('items/create'); ?>">Tambah Item</a>
    <a class="btn btn-success mb-2" href="<?php echo site_url('inbound/scan'); ?>">Inbound (Scan)</a>
    <a class="btn btn-danger mb-2" href="<?php echo site_url('outbound/scan'); ?>">Outbound (Scan)</a>

    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>SKU</th><th>Nama</th><th>Qty</th><th>QR</th></tr></thead>
        <tbody>
        <?php foreach($items as $it): ?>
            <tr>
                <td><?php echo $it->id; ?></td>
                <td><?php echo html_escape($it->sku); ?></td>
                <td><?php echo html_escape($it->name); ?></td>
                <td><?php echo $it->qty; ?></td>
                <td><a class="btn btn-sm btn-outline-info" href="<?php echo site_url('items/qr/'.$it->id); ?>">Lihat QR</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>

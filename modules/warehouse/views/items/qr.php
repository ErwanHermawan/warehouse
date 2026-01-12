<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html><html><head><meta charset="utf-8"><title>QR Item</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head><body class="p-4">
<div class="container">
    <h1>QR Item: <?php echo html_escape($item->name); ?></h1>
    <p>Payload: <code><?php echo 'ITEM:'.$item->id; ?></code></p>
    <img src="<?php echo $qr_url; ?>" alt="QR code">
    <p><a class="btn btn-secondary" href="<?php echo site_url('items'); ?>">Kembali</a></p>
</div>
</body></html>

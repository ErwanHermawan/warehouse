<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html><html><head><meta charset="utf-8"><title>Inbound Scan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
</head><body class="p-4">
<div class="container">
    <h1>Inbound - Scan QR</h1>
    <div id="reader" style="width:500px;"></div>
    <hr>
    <form id="manualForm">
        <div class="form-group">
            <label>Payload (hasil scan)</label>
            <input type="text" id="payload" name="payload" class="form-control">
        </div>
        <div class="form-group">
            <label>Qty</label>
            <input type="number" id="qty" name="qty" class="form-control" value="1">
        </div>
        <button id="submitBtn" class="btn btn-primary">Proses Inbound</button>
    </form>
    <div id="result" class="mt-3"></div>
</div>

<script>
function onScanSuccess(decodedText, decodedResult) {
    document.getElementById('payload').value = decodedText;
    // otomatis stop scanning agar user bisa input qty
    html5QrcodeScanner.clear().catch(error => { console.warn('clear fail', error); });
}

let html5QrcodeScanner = new Html5Qrcode("reader");
const config = { fps: 10, qrbox: 250 };

Html5Qrcode.getCameras().then(cameras => {
    if (cameras && cameras.length) {
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess
        ).catch(err => console.error(err));
    }
}).catch(err => console.warn(err));

document.getElementById('manualForm').addEventListener('submit', function(e){
    e.preventDefault();
    var payload = document.getElementById('payload').value;
    var qty = document.getElementById('qty').value;

    fetch('<?php echo site_url('inbound/process'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'payload='+encodeURIComponent(payload)+'&qty='+encodeURIComponent(qty)
    }).then(r => r.json()).then(j => {
        document.getElementById('result').innerHTML = '<pre>'+JSON.stringify(j, null, 2)+'</pre>';
    });
});
</script>
</body></html>

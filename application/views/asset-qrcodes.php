<style>
    body {
        font-family: Arial, sans-serif;
    }

    .container {
        text-align: center;
        margin-bottom: 20px;
    }

    .btn-print {
        color: #fff;
        background: #2e59d9;
        border: none;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-print:hover {
        background: #1d4ed8;
    }

    .qr-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    figure {
        display: inline-block;
        text-align: center;
        margin: 10px;
    }

    img {
        width: 288px;
        height: 288px;
    }

    figcaption {
        margin-top: 8px;
        font-weight: bold;
    }
</style>

<div class="container">
    <button type="button" class="btn-print" onclick="window.print();">Print</button>
</div>

<div class="qr-container">
    <?php foreach ($equipments as $equipment): ?>
        <?php 
            $chlvalue = "Asset Name: " . $equipment->equipment_name . "\n" .
                        "Asset Number: " . $equipment->equipment_registration . "\n" .
                        "Asset Type: " . $equipment->name;
            $chlvalue = urlencode($chlvalue);
        ?>
        <figure>
            <img src="https://quickchart.io/chart?chs=300x300&cht=qr&chl=<?= $chlvalue; ?>&choe=UTF-8" 
                 alt="QR Code" title="Scan QR Code">
            <figcaption><?= htmlspecialchars($equipment->equipment_registration); ?></figcaption>
        </figure>
    <?php endforeach; ?>
</div>

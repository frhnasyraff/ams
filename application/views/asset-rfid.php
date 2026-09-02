<style>
    body {
        font-family: Montserrat;
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
        font-size: 18px;
        font-weight: bolder;
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
        text-align: left;
        margin: 10px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 8px;
        width: 320px;
    }

    figcaption {
        margin-top: 8px;
        font-weight: bolder;
        font-size: 14px;
        line-height: 1.4em;
    }

    .date {
        margin-bottom: 15px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Hide Print Button in Print View */
    @media print {
        .btn-print {
            display: none !important;
        }
    }
</style>

<div class="container">
    <!-- Print Date -->
    <div class="date">Printed on: <?= date('d M Y, h:i A'); ?></div>

    <!-- Print Button (won't appear in print) -->
    <button type="button" class="btn-print" onclick="window.print();">Print All</button>
</div>

<div class="qr-container">
    <?php foreach ($equipments as $equipment): ?>
        <figure>
            <figcaption>
                <div><strong>Name:</strong> <?= htmlspecialchars($equipment->equipment_name) ?></div>
                <div><strong>RFID:</strong> <?= htmlspecialchars($equipment->rfid) ?></div>
                <div><strong>Type:</strong> <?= htmlspecialchars($equipment->name) ?></div>
                <div><strong>Date of installation:</strong> <?= htmlspecialchars($equipment->date_installed) ?></div>
            </figcaption>
        </figure>

    <?php endforeach; ?>
</div>
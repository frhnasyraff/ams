<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment assets CSV</title>
</head>

<body>
      
    <h6>Equipment Assets Csv</h6>
    <form action="<?= site_url('Common_csv_upload/uploadEquipmentAssets') ?>" method="POST" enctype="multipart/form-data">
        <div>
            <input type="file" name="file" />
        </div>
        <br>
        <div>
            <button type="submit">Upload</button>
        </div>
    </form>



</body>

</html>
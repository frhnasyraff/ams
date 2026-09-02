<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Calibration</title>
   
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            
                <div style="border:1px solid red; padding:10% , 5%; text-align: center; font-weight: bold; color: red; ">
                    <h2> Calibration Alert  </h2>
                </div>
                
            </div>

            <br>

            <div class="col-md-12" >   
                    
                <h3>Asset Calibration : <span style="color:red;"> <?= $expiringAssetsCount ?></span> </h3> 
                <h3>Item Calibration : <span style="color:red;"> <?= $expiringItemsCount ?></span> </h3> 
                    
               
            </div>

          
            <p>Please take the necessary actions for the assets and items requiring calibration</p>
            
        </div>
    </div>

</body>

</html>
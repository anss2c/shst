<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>

        <h2 class="lead">Smart Healthy and Safe Tourism.</h2>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-8">
                <?php  echo yii2mod\google\maps\markers\GoogleMaps::widget([
        
                    'googleMapsUrlOptions' => [
                    'key' => '',
                    'language' => 'id',
                    'version' => '3.1.18',
                    ],
                    'googleMapsOptions' => [
                        'mapTypeId' => 'roadmap',
                        'tilt' => 45,
                        'zoom' => 5,
                    ],
                ]);
        
               ?>
            </div>
            <div class="col-lg-4">
                <h2>Consultation History </h2>

                <p style="height:100px"></p>

            </div>
            <div class="col-lg-4">
                <h2>Health Status </h2>
                <p style="height:100px"></p> 
            </div>    
        </div>

        <div class="row">
            <div class="col-lg-16">
                <div class="col-lg-4">
                    <h2>Cuaca</h2>
                    <p style="height: 100px"></p>
                 </div>
                <div class="col-lg-4">
                    <h2>Angin</h2>
                    <p style="height: 100px"></p>                 
                </div>
                <div class="col-lg-4">
                    <h2>Iklim</h2>
                    <p style="height: 100px"></p>                 
                </div>
            </div>
        </div>

    </div>
</div>

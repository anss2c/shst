<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsService;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\overlays\Polygon;
use dosamigos\google\maps\layers\BicyclingLayer;

$this->title = 'Maps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-maps">
   <h1><?= Html::encode($this->title) ?></h1>
   <?php
       
        $coord = new LatLng(['lat' => -7.591298, 'lng' =>  111.941081]);
        $map = new Map([
                        'center' => $coord,
                        'zoom' => 12,
                        'width' => 900,
                    ]);
                    echo $map->display();
               
   ?>
   <div id="map"> </div>
   

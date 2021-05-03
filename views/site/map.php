<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
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
 $session = Yii::$app->session;
$cuaca=$session['prakirancuaca'];
if(isset($session['prakirancuaca'])){
    $suhu_sekarang=$cuaca['prakiraan_cuaca_besok_wilayah']['suhu_derajat_celcius'];
    $wilayah=$cuaca['prakiraan_cuaca_besok_wilayah']['wilayah'];
    $pagi=$cuaca['prakiraan_cuaca_besok_wilayah']['pagi'];
    $siang=$cuaca['prakiraan_cuaca_besok_wilayah']['siang'];
    $malam=$cuaca['prakiraan_cuaca_besok_wilayah']['malam'];
}
else{
    $suhu_sekarang='Not set';
    $wilayah='Silahkan Pilih Kabupaten';
    $pagi='Not set';
    $siang='Not set';
    $malam='Not set';
}
//print_r($cuaca);
$this->registerCss("
   
    .container2{
      width: 100%;
      max-width: 350px;
      height: 515px;
      border-radius: 10px;
      overflow: hidden;
      position: relative;
    }

    .bg{
      position: absolute;
      z-index: 1;
      top: 0; left: 0; right: 0; bottom: 0;
    }

    .night_bg{
      background: #0F2129;
      background: -webkit-linear-gradient(#0F2129, #47334A); 
      background: -o-linear-gradient(#0F2129, #47334A); 
      background: -moz-linear-gradient(#0F2129, #47334A); 
      background: linear-gradient(#0F2129, #47334A);
    }

    .frosty_bg{
      background: #29386f;
      background: -webkit-linear-gradient(#29386f, #b8f5ff); 
      background: -o-linear-gradient(#29386f, #b8f5ff); 
      background: -moz-linear-gradient(#29386f, #b8f5ff); 
      background: linear-gradient(#29386f, #b8f5ff);
      opacity: 0;
      animation: frostyAnimation 20s ease infinite;
      -webkit-animation: frostyAnimation 20s linear infinite;
    }

    .sunny_bg{
      background: #ffbd3f;
      background: -webkit-linear-gradient(#ffbd3f, #fff097); 
      background: -o-linear-gradient(#ffbd3f, #fff097); 
      background: -moz-linear-gradient(#ffbd3f, #fff097); 
      background: linear-gradient(#ffbd3f, #fff097);
      opacity: 0;
      animation: sunnyAnimation 20s ease infinite;
      -webkit-animation: sunnyAnimation 20s linear infinite;
    }
    @keyframes frostyAnimation {
        32% { opacity: 0; }
        33% { opacity: 1; }
        62% { opacity: 1; }
        63% { opacity: 0; }
    }

    @-webkit-keyframes frostyAnimation {
        32% { opacity: 0; }
        33% { opacity: 1; }
        62% { opacity: 1; }
        63% { opacity: 0; }
    }

    @keyframes sunnyAnimation {
        62%{ opacity: 0; }
        63%{ opacity: 1; }
        99%{ opacity: 1; }
    }

    @-webkit-keyframes sunnyAnimation {
        62%{ opacity: 0; }
        63%{ opacity: 1; }
        99%{ opacity: 1; }
    }

    .text_container{
      margin-top: 50px;
      width: 100%;
      text-align: center;
      font-family: Arial, Helvetica, sans-serif;
      position: relative;
      z-index: 3;
    }

    .degrees{
      color: #4F787D;
      display: block;
      text-indent: -5px;
      font-size: 40px;
      font-weight: bold;
      position: relative;
      animation: degreesAnimation 20s ease infinite;
      -webkit-animation: degreesAnimation 20s linear infinite;
    }

    @keyframes degreesAnimation {
        32% { color: #4F787D; }
        33% { color: #a8ddff; }
        62% { color: #a8ddff; }
        63% { color: #fff5b8; }
        99% { color: #fff5b8; }
    }

    @-webkit-keyframes degreesAnimation {
        32% { color: #4F787D; }
        33% { color: #a8ddff; }
        62% { color: #a8ddff; }
        63% { color: #fff5b8; }
        99% { color: #fff5b8; }
    }

    .degrees::before{
      content: '-';
      animation: degreesTextAnimation 20s ease infinite;
      -webkit-animation: degreesTextAnimation 20s linear infinite;
    }

    @keyframes degreesTextAnimation {
        32% {content: '". $suhu_sekarang."';}
        33% {content: '". $suhu_sekarang."'; }
        62% {content: '". $suhu_sekarang."'; }
        63% {content: '". $suhu_sekarang."'; }
        99% {content: '". $suhu_sekarang."'; }
    }

    @-webkit-keyframes degreesTextAnimation {
        32% {content: '". $suhu_sekarang."';}
        33% {content: '". $suhu_sekarang."'; }
        62% {content: '". $suhu_sekarang."'; }
        63% {content: '". $suhu_sekarang."'; }
        99% {content: '". $suhu_sekarang."'; }
    }

    .degrees span{
      font-size: 20px;
      position: absolute;
      top: 0;
    }

    .place{
      margin-bottom: 10px;
      text-transform: uppercase;
      color: #694c6d;
      animation: placeAnimation 20s ease infinite;
      -webkit-animation: placeAnimation 20s linear infinite;
    }

    @keyframes placeAnimation {
        32% { color: #694c6d; }
        33% { color: #4497bf; }
        62% { color: #4497bf; }
        63% { color: #f7a526; }
        99% { color: #f7a526; }
    }

    @-webkit-keyframes placeAnimation {
        32% { color: #694c6d; }
        33% { color: #4497bf; }
        62% { color: #4497bf; }
        63% { color: #f7a526; }
        99% { color: #f7a526; }
    }

     .place::before{
      content: 'Pilih kabupaten';
      animation: placeTextAnimation 20s ease infinite;
      -webkit-animation: placeTextAnimation 20s linear infinite;
    }

    @keyframes placeTextAnimation {
        32% { content: '". $wilayah."';}
        33% { content: '". $wilayah."'; }
        62% { content: '". $wilayah."'; }
        63% { content: '". $wilayah."'; }
        99% { content: '". $wilayah."'; }
    }

    @-webkit-keyframes placeTextAnimation {
        32% { content: '".$wilayah."';}
        33% { content: '".$wilayah."'; }
        62% { content: '".$wilayah."'; }
        63% { content: '".$wilayah."'; }
        99% { content: '".$wilayah."'; }
    }

    .weather{
      color: #FFF;
    }

     .weather::before{
      content: 'Clear';
      animation: weatherTextAnimation 20s ease infinite;
      -webkit-animation: weatherTextAnimation 20s linear infinite;
    }

    @keyframes weatherTextAnimation {
        32% { content: 'Malam : ". $malam."';}
        33% { content: 'Siang :". $siang."'; }
        62% { content: 'Siang :".$siang."'; }
        63% { content: 'Pagi :".$pagi."'; }
        99% { content: 'Pagi :".$pagi."'; }
    }

    @-webkit-keyframes weatherTextAnimation {
        32% { content: 'Malam : ". $malam."';}
        33% { content: 'Siang :". $siang."'; }
        62% { content: 'Siang :". $siang."'; }
        63% { content: 'Pagi :".$pagi."'; }
        99% { content: 'Pagi :".$pagi."'; }
    }

    .circle_container{
      position: absolute;
      bottom: 200px;
      left: 100px;
      -webkit-animation: circleAnimation 20s linear infinite;
      animation: circleAnimation 20s ease infinite;
    }

    @-webkit-keyframes circleAnimation {
        32% {
           bottom: 200px;
           left: 100px;
        }
        33% {
           bottom: 150px;
           left: 200px;
        }
        62%{
           bottom: 150px;
           left: 200px;
        }
        63%{
           bottom: 400px;
           left: 250px;
        }
        99%{
           bottom: 400px;
           left: 250px;
        }
        100% {
           bottom: 200px;
           left: 100px;
        }
    }
    @keyframes circleAnimation {
        32% {
           bottom: 200px;
           left: 100px;
        }
        33% {
           bottom: 150px;
           left: 200px;
        }
        62%{
           bottom: 150px;
           left: 200px;
        }
        63%{
           bottom: 400px;
           left: 250px;
        }
        99%{
           bottom: 400px;
           left: 250px;
        }
        100% {
           bottom: 200px;
           left: 100px;
        }
    }

    .circle {
        position: relative;
        z-index: 7;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        -ms-box-shadow: 9px 9px 0 0 #BCAE76;
        -o-box-shadow: 9px 9px 0 0 #BCAE76;
        -webkit-box-shadow: 9px 9px 0 0 #BCAE76;
        box-shadow: 9px 9px 0 0 #BCAE76;
        -ms-transform: rotate(100deg);
        -webkit-transform: rotate(100deg);
        -o-transform: rotate(100deg);
        transform: rotate(100deg);
        animation: circleColor 20s ease infinite;
        -webkit-animation: circleColor 20s linear infinite;
    }

    @keyframes circleColor {
        32% {
           box-shadow: 9px 9px 0 0 #BCAE76;
           background: none;
           top: 0;
           left: 0;
        }
        33% {
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #feffdf;
           top: 10px;
           left: -15px;
        }
        62%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #feffdf;
           top: 10px;
           left: -15px;
        }
        63%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #ffdb50;
           top: 10px;
           left: -15px;
        }
        99%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #ffdb50;
           top: 10px;
           left: -15px;
        }
        100% {
           box-shadow: 9px 9px 0 0 #BCAE76;
        }
    }

    @-webkit-keyframes circleColor {
        32% {
           box-shadow: 9px 9px 0 0 #BCAE76;
           background: none;
           top: 0;
           left: 0;
        }
        33% {
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #feffdf;
           top: 10px;
           left: -15px;
        }
        62%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #feffdf;
           top: 10px;
           left: -15px;
        }
        63%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #ffdb50;
           top: 10px;
           left: -15px;
        }
        99%{
           box-shadow: 0px 0px 0 0 #BCAE76;
           background-color: #ffdb50;
           top: 10px;
           left: -15px;
        }
        100% {
           box-shadow: 9px 9px 0 0 #BCAE76;
        }
    }

    .circle1{
      content: '';
      background-color: #BCAE76;
      position: absolute;
      height: 90px;
      width: 90px;
      z-index: 6;
      border-radius: 50%;
      opacity: 0.1;
      top: -5px;
      left: -30px;
      animation: circle1Color 20s ease infinite;
      -webkit-animation: circle1Color 20s linear infinite;
    }

    @keyframes circle1Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }

    @-webkit-keyframes circle1Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }

    .circle2{
      background-color: #BCAE76;
      position: absolute;
      height: 110px;
      width: 110px;
      z-index: 6;
      border-radius: 50%;
      opacity: 0.1;
      top: -15px;
      left: -40px;
      animation: circle2Color 20s ease infinite;
      -webkit-animation: circle2Color 20s ease infinite;
    }

    @keyframes circle2Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }

    @-webkit-keyframes circle2Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }

    .circle3{
      background-color: #BCAE76;
      position: absolute;
      height: 130px;
      width: 130px;
      z-index: 6;
      border-radius: 50%;
      opacity: 0.1;
      top: -25px;
      left: -50px;
      animation: circle3Color 20s ease infinite;
      -webkit-animation: circle3Color 20s ease infinite;
    }

    @keyframes circle3Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }

    @-webkit-keyframes circle3Color {
        32%{ background: #BCAE76; }
        33%{ background: #feffdf; }
        62%{ background: #feffdf; }
        63%{ background: #ffdb50; }
        99%{ background: #ffdb50; }
       100%{ background: #BCAE76; }
    }


    .ground1{
      width: 270px;
      height: 150px;
      border-top-left-radius: 100px;
      border-top-right-radius: 100px;
      position: absolute;
      z-index: 2;
      bottom: -50px;
      left: -20px;
      -ms-transform: rotate(20deg); /* IE 9 */
      -webkit-transform: rotate(20deg); /* Safari */
      transform: rotate(20deg);
    }

    .ground1_night{
      background: #2f2b3c; 
      background: -webkit-linear-gradient(#2f2b3c, #091B21); 
      background: -o-linear-gradient(#2f2b3c, #091B21); 
      background: -moz-linear-gradient(#2f2b3c, #091B21); 
      background: linear-gradient(#2f2b3c, #091B21);
      animation: nightGroundAnimation 20s ease infinite;
      -webkit-animation: nightGroundAnimation 20s ease infinite;
    }

    .ground1_frosty{
      background: #f3ffff; 
      background: -webkit-linear-gradient(#f3ffff, #9af2ff); 
      background: -o-linear-gradient(#f3ffff, #9af2ff); 
      background: -moz-linear-gradient(#f3ffff, #9af2ff); 
      background: linear-gradient(#f3ffff, #9af2ff);
      opacity: 0;
      animation: frostyGroundAnimation 20s ease infinite;
      -webkit-animation: frostyGroundAnimation 20s ease infinite;
    }

    .ground1_sunny{
      background: #e0d7a4; 
      background: -webkit-linear-gradient(#e0d7a4, #e7c77a); 
      background: -o-linear-gradient(#e0d7a4, #e7c77a); 
      background: -moz-linear-gradient(#e0d7a4, #e7c77a); 
      background: linear-gradient(#e0d7a4, #e7c77a);
      opacity: 0;
      animation: sunnyGroundAnimation 20s ease infinite;
      -webkit-animation: sunnyGroundAnimation 20s ease infinite;
    }

    .ground2{
      width: 500px;
      height: 150px;
      border-top-left-radius: 100px;
      border-top-right-radius: 100px;
      position: absolute;
      z-index: 2;
      bottom: -70px;
      right: -80px;
      -ms-transform: rotate(-10deg); /* IE 9 */
      -webkit-transform: rotate(-10deg); /* Safari */
      transform: rotate(-10deg);
    }

    .ground2_night{
      background: #2f2b3c; 
      background: -webkit-linear-gradient(#2f2b3c, #091B21); 
      background: -o-linear-gradient(#2f2b3c, #091B21); 
      background: -moz-linear-gradient(#2f2b3c, #091B21); 
      background: linear-gradient(#2f2b3c, #091B21);
      animation: nightGroundAnimation 20s ease infinite;
      -webkit-animation: nightGroundAnimation 20s ease infinite;
    }

    .ground2_frosty{
      background: #f3ffff; 
      background: -webkit-linear-gradient(#f3ffff, #9af2ff); 
      background: -o-linear-gradient(#f3ffff, #9af2ff); 
      background: -moz-linear-gradient(#f3ffff, #9af2ff); 
      background: linear-gradient(#f3ffff, #9af2ff);
      opacity: 0;
      animation: frostyGroundAnimation 20s ease infinite;
      -webkit-animation: frostyGroundAnimation 20s ease infinite;
    }

    .ground2_sunny{
      background: #e0d7a4; 
      background: -webkit-linear-gradient(#e0d7a4, #e7c77a); 
      background: -o-linear-gradient(#e0d7a4, #e7c77a); 
      background: -moz-linear-gradient(#e0d7a4, #e7c77a); 
      background: linear-gradient(#e0d7a4, #e7c77a);
      opacity: 0;
      animation: sunnyGroundAnimation 20s ease infinite;
      -webkit-animation: sunnyGroundAnimation 20s ease infinite;
    }

    @keyframes nightGroundAnimation {
        32% { opacity: 1; }
        33% { opacity: 0; }
        99% { opacity: 0; }
    }

    @-webkit-keyframes nightGroundAnimation {
        32% { opacity: 1; }
        33% { opacity: 0; }
        99% { opacity: 0; }
    }

    @keyframes frostyGroundAnimation {
        32% { opacity: 0; }
        33% { opacity: 1; }
        62% { opacity: 1; }
        63% { opacity: 0; }
    }

    @-webkit-keyframes frostyGroundAnimation {
        32% { opacity: 0; }
        33% { opacity: 1; }
        62% { opacity: 1; }
        63% { opacity: 0; }
    }

    @keyframes sunnyGroundAnimation {
        62%{ opacity: 0; }
        63%{ opacity: 1; }
        99%{ opacity: 1; }
    }

    @-webkit-keyframes sunnyGroundAnimation {
        62%{ opacity: 0; }
        63%{ opacity: 1; }
        99%{ opacity: 1; }
    }
");

?>
<div class="site-maps">
   <h1><?= Html::encode($this->title) ?></h1>
   <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
                <?php  
                    
                    echo Html::dropDownList('list', "Provinsi", ArrayHelper::map($dataprov, 'provinceId', 'province'),
                                    ['prompt'=>'-Pilih provinsi-',
                                     'class'=>'form-control select2 select2-hidden-accessible',
                                     'id'=>'prov',
			                          'onchange'=>'
				                        $.post( "'.Yii::$app->urlManager->createUrl('site/getkab?id=').'"+$(this).val(), function( data ) {
				                          $( "select#kab" ).html( data );
				                        });
			                        ']); 
                    
                ?>
            </div>
            <div class="col-lg-4">
                <?php  

                   echo Html::dropDownList('list', "Kabupaten",['kosong'=>'0'],
                                    ['prompt' => 'Pilih kabupaten',
                                    'class'=>'form-control select2 select2-hidden-accessible',
                                    'id'=>'kab',
                                    'onchange'=>'
				                        $.post( "'.Yii::$app->urlManager->createUrl('site/getlatlng?id=').'"+$(this).val(), function(data){ 
                                                location.reload();
				                        });'                                                                                                                                                                     
                                    ]);
                ?>
            </div>
        </div>
        <hr>
        <br>
        <div class="row">
            <div class="col-lg-8" id="map">
               <?php
              
               if(isset($session['detailkab']) && isset($session['gplace'])){
                    $detailkab=$session['detailkab'];
                    $detailgplace=$session['gplace'];
                    //print_r($detailkab);
                    $coord = new LatLng(['lat' => $detailkab['lat'], 'lng' => $detailkab['lng']]);
                    $map = new Map([
                                'center' => $coord,
                                'zoom' => 12,
                                'width' => '100%',
                            ]);
                    foreach($detailgplace as $raw){
                        $markercoord = new LatLng(['lat' => $raw['lat'], 'lng' => $raw['lng']]);
                        if($raw['sentimen']==1){
                            $marker = new Marker([
                                'position' => $markercoord,
                                'title' => $raw['name'],
                                'icon' => "http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_blue.png",
                            
                            ]);
                        }  
                        else{
                            $marker = new Marker([
                                'position' => $markercoord,
                                'title' => $raw['name'],
                                'icon' => "http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_red.png",
                            
                            ]);
                        }
                            $marker->attachInfoWindow(
                                new InfoWindow([
                                    'content' => '<p><b>'.$raw['name'].'</b><hr>Alamat :'.$raw['address'].'<br> Rating :'.$raw['rating'].'<br> Total user:'.$raw['user_ratings_total'].' </p>'
                                ])
                            );
                            $map->addOverlay($marker);
                        
                    }
                    echo $map->display();
               }else{
                    $coord = new LatLng(['lat' => $lat, 'lng' =>  $lng]);
                    $map = new Map([
                                'center' => $coord,
                                'zoom' =>6,
                                'width' =>'100%',
                            ]);
                     echo $map->display();
                }
               // print_r($session['gplace']);
               
                ?>
            </div>
            <div class="col-md-4" >
                      <div class="container2">
                          <div class="bg night_bg"></div>
                          <div class="bg frosty_bg"></div>
                          <div class="bg sunny_bg"></div>
                          <div class="text_container">
                            <div class="degrees"><span>&ordm;</span></div>
                            <div class="place"></div>
                            <div class="weather"></div>
                          </div>
                          <div class="circle_container">
                            <div class="circle"></div>
                            <div class="circle1"></div>
                            <div class="circle2"></div>
                            <div class="circle3"></div>
                          </div>
                          <div class="ground1 ground1_night"></div>
                          <div class="ground1 ground1_frosty"></div>
                          <div class="ground1 ground1_sunny"></div>
                          <div class="ground2 ground2_night"></div>
                          <div class="ground2 ground2_frosty"></div>
                          <div class="ground2 ground2_sunny"></div>
                        </div>
            </div>
            
        </div>
        <br>
        <div class="row">
           
                            <div class="box-body">
                                
                                    <div class="col-lg-6 col-xs-12">
                                          <div class="box box-warning">
                                            <div class="box-header with-border">
                                                  <h3 class="box-title">Tweet About This regency</h3>
                                                  <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                  </div>
                                            </div>
                                            <div class="box-body" style="height:200px; overflow-y: scroll">
                                                <?php
                                
                                                    $tweet=$session['detailtweet'];
                                                    if(isset($session['detailkab']) && isset($session['detailtweet'])){
                                                        foreach($tweet as $raw){
                                                            echo $raw['created_at'];
                                                            if($raw['possitive_neutral_negative']==0){
                                                                echo '<div class="comment-text">'.$raw['text'].'</div>';
                                                                echo '<hr>';
                                                            }
                                                            if($raw['possitive_neutral_negative']==1){
                                                                echo '<div class="small-box bg-aqua">'.$raw['text'].'</div>';
                                                                echo '<hr>';
                                                            }
                                                            if($raw['possitive_neutral_negative']==2){
                                                                echo '<div >'.$raw['text'].'</div>';
                                                                echo '<hr>';
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        echo '<div >Silahkan Pilih Wilayah terlebih dahulu</div>';
                                                    }
                                                ?>
                              
                                    </div>
                      </div>
                                    </div>
                                
                                <div class="col-lg-6 col-xs-12">
                                        <section class="content">
                                           <?php 
                                                $gempa=$session['gempa'];
                                                //print_r($gempa );
                                                if(isset($session['gempa'])){
                                                    if($gempa['gempabumi_terkini_wilayah']!=[]){
                                                        echo '<div class="callout callout-danger">
                                                                <h4>Warning!</h4>
                                                                <p>Jarak Gempa dari ibukota : '.$gempa['gempabumi_terkini_wilayah']['jarak_ibukota_dengan_pusat_gempa'].'km <br>
                                                                Waktu Gempa  : '.$gempa['gempabumi_terkini_wilayah']['result']['waktu_gempa'].'<br>
                                                                Lintang     : '.$gempa['gempabumi_terkini_wilayah']['result']['lintang'].'<br>
                                                                Bujur       : '.$gempa['gempabumi_terkini_wilayah']['result']['bujur'].'<br>
                                                                Magnitudo   : '.$gempa['gempabumi_terkini_wilayah']['result']['magnitudo'].'<br>
                                                                Kedalaman   : '.$gempa['gempabumi_terkini_wilayah']['result']['kedalaman'].'<br>
                                                                Wilayah     :'.$gempa['gempabumi_terkini_wilayah']['result']['wilayah'].'<br>
                                                              </p></div>';

                                                     }
                                                     else{
                                                        echo '<div class="callout callout-info">
                                                            <h4>Semoga Aman!</h4>
                                                            <p> Tidak terjadi gempa terkini</p></div>';
                                                     }
                                                }
                                                else {
                                                    echo '<div class="callout callout-info">
                                                            <h4>Semoga Aman!</h4>
                                                            <p> Silahkan Pilih Kabupaten terlebih dahulu</p></div>';
                                                }
                                                ?>
                                          </section>
                                  
                                </div>

                                
                            </div>
            
        </div>

    </div>
   

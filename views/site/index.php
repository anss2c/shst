<?php
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

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>
        <h2 class="lead">Smart Healthy and Safe Tourism.</h2>
    </div>

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
               $session = Yii::$app->session;
               if(isset($session['detailkab']) && isset($session['gplace'])){
                    $detailkab=$session['detailkab'];
                    $detailgplace=$session['gplace'];
                    //print_r($detailkab);
                    $coord = new LatLng(['lat' => $detailkab['lat'], 'lng' => $detailkab['lng']]);
                    $map = new Map([
                                'center' => $coord,
                                //'position'=>'Nganjuk',
                                'zoom' => 15,
                                'width' => 770,
                            ]);
                    foreach($detailgplace as $raw){
                        if($raw['sentimen']==1){
                            $markercoord = new LatLng(['lat' => $raw['lat'], 'lng' => $raw['lng']]);
                            $marker = new Marker([
                                'position' => $markercoord,
                                'title' => $raw['name'],
                                'icon' => "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
                            
                            ]);

                            $marker->attachInfoWindow(
                                new InfoWindow([
                                    'content' => '<p><b>'.$raw['name'].'</b><hr>Alamat :'.$raw['address'].'<br> Rating :'.$raw['rating'].'<br> Total user:'.$raw['user_ratings_total'].' </p>'
                                ])
                            );
                            $map->addOverlay($marker);
                        }
                    }
                    echo $map->display();
               }else{
                    $coord = new LatLng(['lat' => $lat, 'lng' =>  $lng]);
                    $map = new Map([
                                'center' => $coord,
                                'zoom' =>15,
                                'width' => 770,
                            ]);
                     echo $map->display();
                }
               // print_r($session['gplace']);
               
                ?>
            </div>
            <div class="col-md-4">
                      <div class="box box-warning">
                            <div class="box-header with-border">
                                  <h3 class="box-title">Tweet About This regency</h3>
                                  <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                  </div>
                            </div>
                            <div class="box-body">
                                <?php
                                /*
                                    $tweet=$session['detailtweet'];
                                    if(isset($session['detailkab']) && isset($session['detailtweet'])){
                                        foreach($tweet as $raw){
                                            
                                        }
                                    }
                                    print_r($tweet)
                                    */
                                ?>
                                <p style="height:150px"></p>
                            </div>
                      </div>
            </div>
            <div class="col-md-4">
                <div class="box box-warning">
                            <div class="box-header with-border">
                                  <h3 class="box-title">Health Status</h3>
                                  <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                  </div>
                            </div>
                            <div class="box-body">
                                <p style="height:150px"></p>
                            </div>
                </div>
            </div>    
        </div>
        <br>
        <div class="row">
            <div class="col-lg-16 box box-warning">
                
                            <div class="box-header with-border">
                                  <h3 class="box-title">Prediksi Iklim dan Cuaca</h3>
                                  <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                  </div>
                            </div>
                            <div class="box-body">
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
</div>

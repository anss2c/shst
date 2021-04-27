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
                                'zoom' => 10,
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
            <div class="col-md-4">
                <div class="box box-warning">
                            <div class="box-header with-border">
                                  <h3 class="box-title">Health Status</h3>
                                  <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                  </div>
                            </div>
                            <div class="box-body" style="height:200px; overflow-y: scroll">
                                <?php
                                    $periksa = $session['pesanSes'];
                                    $i=0;
                                if(isset($session['pesanSes'])){
                                    foreach($periksa as $raw){
                                        if($raw['sender']=='Patient'){
                                            echo 'Waktu konsultasi :'.$raw['time'].'<br>';
                                            echo 'Keluhan :'.str_replace('GEJALA','',$raw['message']).'<br>';
                                        }
                                        if($raw['sender']=='mesin' && $i>2){
                                            echo 'Hasil :'.$raw['message'].'<br>';
                                            echo '<hr>';
                                        }
                                        $i++;
                                    }
                                }
                                else{
                                        echo '<div >Silahkan Mencoba Halaman medical Chatbot</div>';
                                    }
                                ?>
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
                                
                                    <div class="col-lg-3 col-xs-6">
                                          <!-- small box -->
                                          <div class="small-box bg-aqua">
                                            <div class="inner">
                                              <h3>On Process</h3>

                                              <p>Temperatur</p>
                                            </div>
                                            <div class="icon">
                                              <i class="fa fa-sun-o"></i>
                                            </div>
                                            <a href="#" class="small-box-footer">
                                              
                                            </a>
                                          </div>
                                    </div>
                                
                                <div class="col-lg-3 col-xs-6">
                                  <!-- small box -->
                                  <div class="small-box bg-green">
                                    <div class="inner">
                                      <h3>On Process<sup style="font-size: 20px">%</sup></h3>

                                      <p>Temperature</p>
                                    </div>
                                    <div class="icon">
                                      <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                      
                                    </a>
                                  </div>
                                </div>

                                <div class="col-lg-3 col-xs-6">
                                          <!-- small box -->
                                          <div class="small-box bg-aqua">
                                            <div class="inner">
                                              <h3>On Process</h3>

                                              <p>Temperatur</p>
                                            </div>
                                            <div class="icon">
                                              <i class="fa fa-sun-o"></i>
                                            </div>
                                            <a href="#" class="small-box-footer">
                                              
                                            </a>
                                          </div>
                                    </div>
                            </div>
            </div>
        </div>

    </div>
</div>

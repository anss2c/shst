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
    <div style="text-align: center;">
        <h2>Welcome! Smart Healthy and Safe Tourism.</h2>
       
    </div>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-16" id="map">
               <?php
                    $session = Yii::$app->session;
                    $coord = new LatLng(['lat' => $lat, 'lng' => $lng]);
                    $map = new Map([
                                'center' => $coord,
                                'zoom' => 5,
                                'width' => '100%',
                            ]);
                    foreach($datagplace as $raw){
                        $markercoord = new LatLng(['lat' => $raw['lat'], 'lng' => $raw['lng']]);
                        if($raw['rating']>=3 && $raw['user_ratings_total']>=500){
                            $marker = new Marker([
                                'position' => $markercoord,
                                'title' => $raw['name'],
                            
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
                
                ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-16">
                            <div class="box-body">
                                    <div class="col-lg-4 col-xs-8">
                                            <div class="box box-warning">
                                                        <div class="box-header with-border">
                                                              <h3 class="box-title">Gempa Bumi Terkini</h3>
                                                              <div class="box-tools pull-right">
                                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                              </div>
                                                        </div>
                                                        <div class="box-body" style="height:150px; overflow-y: scroll">
                                                            <?php
                                                                
                                                            $i=0;
                                                            if($datagempa != null){
                                                                foreach($datagempa as $raw){
                                                                    foreach($raw as $baris){
                                                                        echo 'Waktu Gempa :'.$baris['waktu_gempa'].'<br>';
                                                                        echo 'Lokasi :'.$baris['wilayah'].'<br>';
                                                                        echo 'Magnitudo :'.$baris['magnitudo'].'<br>';
                                                                        echo '<hr>';
                                                                        
                                                                    }
                                                                }
                                                                 
                                                            }
                                                            else{
                                                                    echo '<div>Tidak ada gempa akhir-akhir ini di Indonesia</div>';
                                                                }
                                                           
                                                            ?>
                                                        </div>
                                            </div>
                                    </div>        
                                
                                <div class="col-lg-4 col-xs-8">
                                        <div class="box box-warning">
                                                        <div class="box-header with-border">
                                                              <h3 class="box-title">Objek Wisata Terfavorit</h3>
                                                              <div class="box-tools pull-right">
                                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                              </div>
                                                        </div>
                                                        <div class="box-body" style="height:150px; overflow-y: scroll">
                                                            <?php
                                                                
                                                            $i=0;
                                                            if($datagplace != null){
                                                                foreach($datagplace as $raw){
                                                                    if($raw['sentimen']==1 && $raw['user_ratings_total']>=10000){
                                                                       echo 'Objek Wisata :'.$raw['name'].'<br>';
                                                                       echo 'Lokasi :'.$raw['address'].'<br>';
                                                                       echo 'User Rating :'.$raw['user_ratings_total'].'<br>';
                                                                       echo 'Rating :'.$raw['rating'].'<br>';
                                                                       echo '<hr>';
                                                                     }   
                                                                     $i++;
                                                                }
                                                                 
                                                            }
                                                            else{
                                                                    echo '<div>Tidak ada gempa akhir-akhir ini di Indonesia</div>';
                                                                }
                                                           //print_r($datagplace);
                                                            ?>
                                                        </div>
                                            </div>
                                </div>

                                <div class="col-lg-4 col-xs-8">
                                            <div class="box box-warning">
                                                        <div class="box-header with-border">
                                                              <h3 class="box-title">Health Status</h3>
                                                              <div class="box-tools pull-right">
                                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                              </div>
                                                        </div>
                                                        <div class="box-body" style="height:150px; overflow-y: scroll">
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
            </div>
        </div>

    </div>
</div>

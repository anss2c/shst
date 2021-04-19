<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Maps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-maps">
   <h1><?= Html::encode($this->title) ?></h1>
   <?php  echo yii2mod\google\maps\markers\GoogleMaps::widget([
        
        'googleMapsUrlOptions' => [
        'key' => 'AIzaSyChugesfBjF1aN9VfCYN1scMo516JtsGjk',
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

<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Medical Chatbot';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-maps">
    <h1><?= Html::encode($this->title) ?></h1>
  <?php echo \TomLutzenberger\Smartsupp\SmartsuppChat::widget([
    'key' => 'f6875dd8588c4078858578c77917b452ab6911ab'])?>
</div>
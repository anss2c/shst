<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
     
    <title><?= Html::encode("Smart Healthy and Safe Tourism") ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/logo1.png',['width' => '200px','height' => '35px']),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [ 
            'class' => 'navbar-inverse navbar-fixed-top',
            'style' => 'background-color : #3e4551; background-border : #3e4551'
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav1 navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index'], 'style' => 'color:red'],
            ['label' => 'Maps', 'url' => ['/site/map']],
            ['label' => 'Medical Chatbot', 'url' => ['/site/telemedicine']],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>
    
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer" style="background-color : #86b8a5">
    <div class="container">
        <p class="pull-left">&copy; PLTI <?= date('Y') ?></p>

        <p class="pull-right"></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Medical Chatbot';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs(
                    "
                    
                    $('#kirimPesan').on('click', function(e) {       
                        $.ajax({ 
                        url:'".Yii::$app->urlManager->createUrl(['site/telemedicine'])."',
                        type: 'post' ,
                        data: {pesan : $('#pesan').val()},
                        success: function (data) {
                            $('#pesan').val('');
                            $('#kotakPesan').html(data);  
                        },
                        });
                    });
                    
                    ",   
                       
                \yii\web\View::POS_READY
                );
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-md-16">
          <!-- DIRECT CHAT SUCCESS -->
          <div class="box box-success direct-chat direct-chat-success">
            <div class="box-header with-border">
              <h3 class="box-title">Direct Chat</h3>

              <div class="box-tools pull-right">
                <span data-toggle="tooltip" title="0 New Messages" class="badge bg-green"></span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                  <i class="fa fa-comments"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- Conversations are loaded here -->
              <div class="direct-chat-messages"  style="height:430px" id="kotakPesan">
                <!-- Message. Default to the left -->
                <?php 
                    
                    foreach($pesan as $raw){
                        if($raw['sender']=="mesin"){ ?>
                            <div class="direct-chat-msg">
                              <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-left"><?php echo $raw['name'] ?></span>
                                <span class="direct-chat-timestamp pull-right"><?php echo $raw['time'] ?></span>
                              </div>
                              <!-- /.direct-chat-info -->
                              <?php echo Html::img('@web/chatbot.png',['class'=>'direct-chat-img']) ?>
                              <div class="direct-chat-text">
                                <?php echo $raw['message'] ?>
                              </div>
                              <!-- /.direct-chat-text -->
                            </div>
                <?php   }
                        else{  ?>
                            <div class="direct-chat-msg right">
                              <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-right"><?php echo $raw['name'] ?></span>
                                <span class="direct-chat-timestamp pull-left"><?php echo $raw['time'] ?></span>
                              </div>
                              <!-- /.direct-chat-info -->
                              <?php echo Html::img('@web/pasien.png',['class'=>'direct-chat-img']) ?><!-- /.direct-chat-img -->
                              <div class="direct-chat-text">
                                <?php echo $raw['message'] ?>
                              </div>
                              <!-- /.direct-chat-text -->
                            </div>
                <?php   }
                    }
                ?>
              </div>
              
            <div class="box-footer">
              <form action="#" method="post">
                <div class="input-group">
                  <input type="text" name="message" placeholder="Type Message ..." class="form-control" id="pesan">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-success btn-flat" id="kirimPesan">Send</button>
                      </span>
                </div>
              </form>
            </div>
            <!-- /.box-footer-->
          </div>
          <!--/.direct-chat -->
        </div>
        <!-- /.col --> 
</div>

<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\httpclient\Client;
use yii\helpers\Json;
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

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $successUrl = '';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
                'successUrl' => [$this, 'index']
            ],
        ];
    }
    public function successCallback($client)
    {
        $attributes = $client->getUserAttributes();
        // user login or signup comes here
        $user = \common\models\User::find()
        ->where([
            'email'=>$attributes['email'],
        ])
        ->one();
        if(!empty($user)){
            Yii::$app->user->login($user);
        }
        else{
            //Simpen disession attribute user dari Google
            $session = Yii::$app->session;
            $session['attributes']=$attributes;
            // redirect ke form signup, dengan mengset nilai variabell global successUrl
            $this->successUrl = \yii\helpers\Url::to(['signup']);
        }   
    }
    public function actionSignup()
    {
 
        $model = new SignupForm();
 
        // Tambahkan ini aje.. session yang kita buat sebelumnya, MULAI
        $session = Yii::$app->session;
        if (!empty($session['attributes'])){
            $model->username = $session['attributes']['first_name'];
            $model->email = $session['attributes']['email'];
        }
        // SELESAI
 
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
 
        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        ob_start();
        $session = Yii::$app->session;
        $lat= 0.459983;
        $long= 115.572010;
        $client = new Client(['baseUrl' => 'http://healthysafetourismdev.herokuapp.com/']);
        $response = $client->createRequest()
           ->setUrl('province')
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $data = Json::decode($response->content, true);

        $responsegempa = $client->createRequest()
           ->setUrl('gempabumi')
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $datagempa = Json::decode($responsegempa->content, true);

         $responsegplace = $client->createRequest()
           ->setUrl('gplace')
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $datagplace = Json::decode($responsegplace->content, true);
        //$rating=array_column($datagplace, 'rating');
        $user_rating = array_column($datagplace, 'user_ratings_total');
        $sort_gplace=array_multisort($user_rating, SORT_DESC, $datagplace);
       
        return $this->render('index', ['dataprov' =>$data, 'datagempa'=>$datagempa,'datagplace'=>$datagplace,'lat'=>$lat, 'lng'=>$long]);
    }

    public function actionGetkab($id){
        
        $client = new Client(['baseUrl' => 'http://healthysafetourismdev.herokuapp.com/']);
        $response = $client->createRequest()
           ->setUrl('/regency/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $data = Json::decode($response->content, true);
        
        if (!empty($data)) {
			foreach($data as $post) {
				echo "<option value='".$post['regencyId']."'>".$post['regency']."</option>";
			}
		} else {
			echo "<option>-</option>";
		}
    }
    public function actionGetlatlng($id){
        ob_start();
        $session = Yii::$app->session;
        if(isset($session['detailkab'])){
            unset($session['detaikab']);
        }
        $client = new Client(['baseUrl' => 'http://healthysafetourismdev.herokuapp.com/']);
        $responsekab = $client->createRequest()
           ->setUrl('/regencydetail/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $detailkab = Json::decode($responsekab->content, true);

        $responseprov = $client->createRequest()
           ->setUrl('province')
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $dataprov = Json::decode($responseprov->content, true);

        $responsePlace = $client->createRequest()
           ->setMethod('GET')
           ->setUrl('/gplace/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $gplace = Json::decode($responsePlace->content, true);
        
        $responsetweet = $client->createRequest()
           ->setMethod('GET')
           ->setUrl('/tweetall/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $tweet = Json::decode($responsetweet->content, true);

        $responseCuaca = $client->createRequest()
           ->setUrl('/prakiraancuacakabkota/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $cuaca = Json::decode($responseCuaca->content, true);

        $responseGempa = $client->createRequest()
           ->setUrl('/gempabumi/'.$id)
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $gempa = Json::decode($responseGempa->content, true);
        

        $session->open();
        $session->set('gplace', $gplace);
        $session->set('detailkab', $detailkab);
        $session->set('detailtweet', $tweet);
        $session->set('prakirancuaca', $cuaca);
        $session->set('gempa', $gempa);
        $session->close();
        return $this->render('map', ['dataprov'=>$dataprov, 'detailkab'=>$detailkab, 'gplace'=>$gplace], false, true);
          
    }
    public function actionSendChat() {
        if (!empty($_POST)) {
            echo \sintret\chat\ChatRoom::sendChat($_POST);
        }
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
    public function actionMap()
    {
        $lat= 0.459983;
        $long= 115.572010;
        $client = new Client(['baseUrl' => 'http://healthysafetourismdev.herokuapp.com/']);
        $response = $client->createRequest()
           ->setUrl('province')
           ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
           ->send();
        $data = Json::decode($response->content, true);

        
        return $this->render('map', ['dataprov' =>$data, 'lat'=>$lat, 'lng'=>$long]);
    }
    public function actionTelemedicine()
    {
        $session = Yii::$app->session;
        $pesan=array(
              array("sender"=>"mesin",
                    "name" =>"SHST medical Chatbot",
                    "message" => "Hello!!",
                    "time"  =>date('Y-m-d H:i:s'),
                    "avatar" =>""
              ),
              array("sender"=>"mesin",
                    "name" =>"SHST medical Chatbot",
                    "message" => "We are Medical Chatbot powered by Smart Health dan Safe Tourism",
                    "time"  =>date('Y-m-d H:i:s'),
                    "avatar" =>""
              ),
              array("sender"=>"mesin",
                    "name" =>"SHST medical Chatbot",
                    "message" => "Please, Feel free to check your condition with us!",
                    "time"  =>date('Y-m-d H:i:s'),
                    "avatar" =>""
              ),

       );
       if (Yii::$app->getRequest()->isAjax) {
            ob_start();
            $pesanMasuk = $_POST['pesan'];
            $date = date('Y-m-d H:i:s');
            if(isset($session['pesanSes'])){
                $pesanArr=$session['pesanSes'];
            }
            else{
                $pesanArr=$pesan;
            }
            array_push($pesanArr, array("sender"=>"Patient",
                    "name" =>"Patient",
                    "message" => $pesanMasuk,
                    "time"  =>$date,
                    "avatar" =>""));
            $client = new Client(['baseUrl' => 'http://healthysafetourismdev.herokuapp.com/']);
            $pesanMasukconv = preg_replace('/\s+/', '%20', $pesanMasuk);;
            //print($pesanMasukconv);
            $response = $client->createRequest()
               ->setUrl('/medicalchatbot/"'.$pesanMasukconv.'"')
               ->setHeaders(['content-type' => 'application/json', 'access_token' => 'ywoU6gU5zWA1IdUHurDXTGhJwHWAqm'])
               ->send();
            $databalasan = Json::decode($response->content, true);  
            //print_r($databalasan);
            $balasan=$databalasan['medibot'];
            array_push($pesanArr, array("sender"=>"mesin",
                    "name" =>"SHST medical Chatbot",
                    "message" => $balasan,
                    "time"  =>$date,
                    "avatar" =>""));
            foreach($pesanArr as $raw){
                        if($raw['sender']=="mesin"){ 
                          echo '<div class="direct-chat-msg">
                                  <div class="direct-chat-info clearfix">
                                    <span class="direct-chat-name pull-left">'.$raw['name'].' </span>
                                    <span class="direct-chat-timestamp pull-right">'.$raw['time'].'</span>
                                  </div>'.
                                  Html::img('@web/chatbot.png',['class'=>'direct-chat-img']).'
                                  <div class="direct-chat-text">'.$raw['message'].'</div>
                                </div>';
                        }
                        else{  
                           echo '<div class="direct-chat-msg right">
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-right">'.$raw['name'].'</span>
                                        <span class="direct-chat-timestamp pull-left">'.$raw['time'].'</span>
                                      </div>'.
                                      Html::img('@web/pasien.png',['class'=>'direct-chat-img']).'
                                      <div class="direct-chat-text">'.$raw['message'].'</div>
                                </div>';
                        } 
            $session->open();
            $session->set('pesanSes', $pesanArr);
            $session->close();
            }

       }else {
            unset($session['pesanSes']);
	        return $this->render('telemedicine', ['pesan'=>$pesan]);
       }  
    }
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

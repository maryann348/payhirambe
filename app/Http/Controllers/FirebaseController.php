<?php

namespace App\Http\Controllers;
use Kreait\Firebase\Factory;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging;
class FirebaseController extends APIController
{
	public $messaging;
  function __construct(){
  	$factory = (new Factory)->withServiceAccount(app_path('http\controllers\payhiram-firebase-adminsdk-nn06x-910d50fc3a.json'));
  	$this->messaging = $factory->createMessaging();
  }


  public function send(Request $request){
  	$data = $request->all();
  	$message = CloudMessage::fromArray([
	    'topic' => $data['topic'],
	    'notification' => $data['notification'], // optional
	    'data' => $data['data'], // optional
		]);
		$this->messaging->send($message);
  }

  public function sendLocal($data){
  	$message = CloudMessage::fromArray([
	    'topic' => $data['topic'],
	    'notification' => $data['notification'], // optional
	    'data' => $data['data'], // optional
		]);
		$this->messaging->send($message);
  }

}

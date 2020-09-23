<?php

namespace App\Http\Controllers;

use App\Http\Controllers\APIController;
use App\MyCircle;
use Increment\Account\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use Mail;
class MyCircleController extends APIController
{
    function __construct(){
		$this->model = new MyCircle();
    }
    
    public function create(Request $request){
    $data = $request->all();
         $receipient = Account::where('email', '=', $data['to_email'])->get();
         $exist = $this->checkIfExist($data['to_email']);
            if($exist == false){
               $user = $this->retrieveAccountDetails($data['account_id']);
               $insertData = array(
                   'code' => $this->generateCode(),
                   'account_id'	=> $data['account_id'],
                   'account'	=> $receipient[0]->id,
                   'status'	=> 'sent'
               );
               $this->model = new MyCircle();
               $this->insertDB($insertData);
               if($this->response['data'] > 0 && $user != null){
                  app('App\Http\Controllers\EmailController')->invitation($user, $data);
               }
               return $this->response();
            }else{
                $this->response['data'] = null;
                $this->response['error'] = $exist;
                return $this->response();
            }
        }

	public function checkIfExist($email){
      $account = Account::where('email', '=', $email)->get();
		if(sizeof($account) == 0){
			return 'Email does not exist exist';
		}else{
         $invites = MyCircle::where('account', '=', $account[0]->id)->get();
			return (sizeof($invites) > 0) ? 'Email Address was already invited.' : false;
		}
	}

   public function getDetails($id){
      $result = MyCircle::where('id', '=', $id)->get();
      return (sizeof($result) > 0) ? $result[0] : null;
   }

	public function generateCode(){
      $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 64);
      $codeExist = MyCircle::where('id', '=', $code)->get();
      if(sizeof($codeExist) > 0){
        $this->generateCode();
      }else{
        return $code;
      }
   }

   public function confirmReferral($code){
      $result = MyCircle::where('code', '=', $code)->update(array(
         'status' => 'confirmed',
         'updated_at' => Carbon::now()
      ));

      $referrral = MyCircle::where('code', '=', $code)->get();
      
      if(sizeof($referrral) > 0){
         app('App\Http\Controllers\EmailController')->notifyReferrer($referrral[0]['account_id']);
      }
   }

   public function generateLink(){
      $this->response["link"] = $this->generateCode();
      return $this->response();
   }
}

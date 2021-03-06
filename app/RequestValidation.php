<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestValidation extends APIModel
{
  protected $table = 'request_validations';
  protected $fillable = ['request_id', 'account_id', 'payload', 'status'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getRequestIdAttribute($value){
    return intval($value);
  }
}

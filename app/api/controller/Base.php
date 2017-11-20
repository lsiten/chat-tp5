<?php
/*
 * @Author: lsiten 
 * @Date: 2017-11-19 15:28:28 
 * @Last Modified by: lsiten
 * @Last Modified time: 2017-11-19 18:29:51
 */
namespace app\api\controller;
use think\Controller;

class Base extends Controller{
    public function _initialize(){
        $type = input('get.type');
        if('xml'==$type)
        {
          config('default_return_type','xml');
        }
        else if('jsonp'==$type)
        {
          header('Access-Control-Allow-Origin: *');
          config('default_return_type','jsonp');
        }
        else{
          header('Access-Control-Allow-Origin: *');
          header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
          header('Access-Control-Allow-Methods: GET, POST, PUT');
        }
    }
}
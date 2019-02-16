<?php

namespace App\Http\Controllers\Move;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{
    public function index(){
        $seat_key='move_seat';
        $seat_status=[];
        for($i=1;$i<=30;$i++){
            $status=Redis::getBit($seat_key,$i);
            $seat_status[$i]=$status;
            $data=[
                'seat'=>$seat_status
            ];
        }
        return view('move.index',$data);
    }
    public function order(Request $request){
        $key='move_seat';
        $position=$request->input('position');
        //echo $position."<br/>".$status;
        $res=Redis::setBit($key,$position,1);
            $response=[
                'msg'=>'预订成功，等待发票'
            ];
            return $response;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCardController extends Controller
{
    public function buy(Request $request){
        $response = ['status' => 1 ,'msg' => null];
        $search = $request->input('busqueda');
        $Query = DB::table('sales')
                    ->join('cards','card_id','cards.id')
                    ->join('users','user_id','users.id')
                    ->select('cards.name as CardName','numberCards','price','users.name')
                    ->where('cards.name','like','%'. $search .'%')
                    ->orderBy('price','asc')
                    ->get();
                    
        $response['msg']['info'] = 'Su busqueda:';
        $response['msg']['data'] = $Query;
        

        return response()->json($response,200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\card_collection;
use Illuminate\Http\Request;
use App\Models\Cards;
use App\Models\collection;
use App\Models\Sales;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CardsController extends Controller
{
    public function registerCard(Request $request){

        $response = ['status' => 1 ,'msg' => null];

        if (isset($request)) {

            $validatedData = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'collection' => 'required|exists:collections,id'
            ]);

            if ($validatedData->fails()) {
                $response['status'] = 0;
                $response['msg']['info'] = "Invalid format";
                $response['msg']['error'] = $validatedData->errors();
                return response()->json($response, 400);
            }else{
                try{
                    $card = new Cards();
                    $card->name = $request->input('name');
                    $card->description = $request->input('description');
                    $card->save();
                    
                    $newDeck = new card_collection();
                    $newDeck->card_id = $card->id;
                    $newDeck->collection_id = $request->input('collection');
                    $newDeck->save();

                    $collection = collection::find($request->input('collection'));
                    $collection->edition_date = new DateTime('now');
                    $collection->save();

                    $response['msg']['info'] = 'Card created';

                    return response()->json($response, 200);
                }catch(\Exception $e){
                    $response['status'] = 0;
                    $response['msg']['info'] = 'Regist fail';
                    $response['msg']['error'] = $e;
                    return response()->json($response,400);
                }
            }
        }else{
            $response['msg']['info'] = 'introduce valores';
            return response()->json($response);
        }
    }

    public function addCardToCollection(Request $request){

        $response = ['status' => 1 ,'msg' => null];

        $validatedData = Validator::make($request->all(),[
            'card_id' => 'required|int|exists:cards,id',
            'collection_id' => 'required|int|exists:collections,id'
        ]);
        if($validatedData->fails()){
            $response['status'] = 0;
            $response['msg']['info'] = "Invalid format";
            $response['msg']['error'] = $validatedData->errors();
            return response()->json($response, 400);
        }else{
            $card = Cards::find($request->input('card_id'));
            $collection = collection::find($request->input('collection_id'));
    
            $Query = DB::table('card_collections')
                            ->where('card_id',$request->input('card_id'))
                            ->where('collection_id',$request->input('collection_id'))
                            ->value('id');
    
            if($Query){
                $response['status'] = 0;
                $response['msg']['info'] = 'Card is already associated';
                return response()->json($response,400);
            }else{
                $newDeck = new card_collection();
                $newDeck->card_id = $card->id;
                $newDeck->collection_id = $collection->id;
                $newDeck->save();
    
                $collection->edition_date = new DateTime('now');
                $collection->save();
    
                $response['msg']['info'] = 'Card associated correctly';
    
                return response()->json($response, 200);
            }
        }
       
    }

    public function saleCards(Request $request){
        $response = ['status' => 1 ,'msg' => null];

        if(isset($request)){
            $validatedData = Validator::make($request->all(),[
                'card_id' => 'required|exists:cards,id',
                'number_cards' => 'required',
                'price' => 'required|Numeric'
            ]);

            if($validatedData->fails()){
                $response['status'] = 0;
                $response['msg']['info'] = "Invalid format";
                $response['msg']['error'] = $validatedData->errors();
                return response()->json($response, 400);
            }else{
                try{
                    $user = $request->user();
                    $sales = new Sales();
                    $sales->user_id = $user->id;
                    $sales->card_id = $request->input('card_id');
                    $sales->numberCards = $request->input('number_cards');
                    $sales->price = $request->input('price');

                    $sales->save();

                    $response['msg']['info'] = 'Card offered to sale succesfully';

                    return response()->json($response, 200);

                }catch(\Exception $e){
                    $response['status'] = 0;
                    $response['msg']['info'] = 'Offer fail';
                    $response['msg']['error'] = $e;
                    return response()->json($response,400);
                }
            }
        }
    }

    public function searchCard(Request $request){
        if(isset($request)){
            try{
                $search = $request->input('busqueda');

                $Query = DB::table('cards')
                            ->select('name', 'id')
                            ->where('name','like','%' . $search . '%')
                            ->get();

                $response['msg']['info'] = 'Your search:';
                $response['msg']['data'] = $Query;
                            
                            
                return response()->json($response, 200);

            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg']['info'] = 'Search fail';
                $response['msg']['error'] = $e;
                return response()->json($response,400);
            }
        }
    }
}

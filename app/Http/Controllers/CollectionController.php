<?php

namespace App\Http\Controllers;

use App\Models\card_collection;
use App\Models\Cards;
use App\Models\collection;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    public function createCollection(Request $request){

        $response = ['status' => 1 ,'msg' => null];

        if(isset($request)){
            $validatedCollection = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'symbol' => 'required|string|max:255',
                'card_id' => 'nullable|exists:cards,id'
            ]);

            $validatedCard = Validator::make($request->all(),[
                'name_card' => 'required|string|max:255',
                'description_card' => 'required|string|max:255',
            ]);

            try{
                if(!$request->input('card_id')){
                    if($validatedCard->fails() || $validatedCollection->fails()){
                        $response['status'] = 0;
                        $response['msg']['info'] = "Invalid format";
                        $response['msg']['error'] = $validatedCollection->errors() . $validatedCard->errors();

                        return response()->json($response, 400);
                    }else{
                        $date = new DateTime('now');

                        $collection = new collection();
    
                        $collection->name = $request->input('name');
                        $collection->symbol = $request->input('symbol');
                        $collection->edition_date = $date;
                        $collection->save();

                        $card = new Cards();
                        $card->name = $request->input('name_card');
                        $card->description = $request->input('description_card');
                        $card->save();

                        $newDeck = new card_collection();
                        $newDeck->card_id = $card->id;
                        $newDeck->collection_id = $collection->id;
                        $newDeck->save();

                        $response['msg']['info'] = 'Collection and card created';

                        return response()->json($response, 200);


                    }
                }else{
                    if($validatedCollection->fails()){
                        $response['status'] = 0;
                        $response['msg']['info'] = "Invalid format";
                        $response['msg']['error'] = $validatedCollection->errors();
                        return response()->json($response, 400);
                    }else{
                        $date = new DateTime('now');

                        $collection = new collection();
    
                        $collection->name = $request->input('name');
                        $collection->symbol = $request->input('symbol');
                        $collection->edition_date = $date;
                        $collection->save();

                        $newDeck = new card_collection();
                        $newDeck->card_id = $request->input('card_id');
                        $newDeck->collection_id = $collection->id;
                        $newDeck->save();

                        $response['msg']['info'] = 'Collection created and card associated';

                        return response()->json($response, 200);

                    }
                }
            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg']['info'] = 'Regist fail';
                $response['msg']['error'] = $e;
                return response()->json($response,400);
            }  
        }

    }
}

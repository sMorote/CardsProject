<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request){

        $response = ['status' => 1 ,'msg' => null];

        $validatedData = Validator::make($request->all(),[
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|regex:/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
            'role' => ['required',Rule::in('Particular','Profesional','Administrador')]
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['msg']['info'] = "Invalid format";
            $response['msg']['error'] = $validatedData->errors();
            return response()->json($response, 400);
        }else{
            try{


                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'role' => $request->input('role')
                ]);

                $response['msg']['info'] = 'Register complete succesfully';
                return response()->json($response);
            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg']['info'] = 'Register failed';
                $response['msg']['error'] = $e;
                return response()->json($response,400);
            }
        }

    }

    public function getUser(Request $request){
        return response()->json($request->user());
    }

    public function login(Request $request){

        if (!Auth::attempt($request->only('name', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('name',$request['name'])->firstOrFail();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_Token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function recoverPass(Request $request){


        $Pass_pattern = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/";

        $user = User::where('email',$request->email)->first();

        if ($user) {
            do{
                $password = Str::random(8);
            }while(!preg_match($Pass_pattern, $password));
            $user->password = Hash::make($password);
            $user->save();

            return response()->json([
            'new password' => $password
        ]);
        }else{
            return response()->json([
                'message' => 'Not find email in database'
            ], 404);
        }
    }

     /**
     * Redirect To Google OAuth
     * 
     * @param nil
     * @return response()->json($response, http_status_code)
     */
    public function loginRedirect()
    {
        $response = ["status" => 1, "msg" => ""];

        try {
            $googleAuthScopes = [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ];

            $response['msg'] = Socialite::driver('google')->scopes($googleAuthScopes)->redirect()->getTargetUrl();
            $response['status'] = 1;

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }

    /**
     * Get oAuth Data
     * 
     * @param nil
     * @return response()->json($response, http_status_code)
     */
    public function loginCall()
    {
        $response = ["status" => 1, "msg" => ""];

        try {
            $auth_user = Socialite::driver('google')->stateless()->user();

            $dbUser = User::where('email', $auth_user->email)->first();

            if (!$dbUser) {
                $user = new User();
                $user->google_id = $auth_user->id;
                $user->name = $auth_user->name;
                $user->email = $auth_user->email;
                $user->save();

                $token = $user->createToken('auth_token')->plainTextToken;

                $response['msg'] = "Usuario Guardado Correctamente";
                $response['token'] = $token;
                $response['status'] = 1;

                return response()->json($response, 200);
            } elseif($dbUser) {
                $user = User::where('email',$auth_user->email)->first();
                $user->tokens()->delete();
                $token = $user->createToken('auth_token')->plainTextToken;

                $response['msg'] = "Usuario Guardado Correctamente";
                $response['token'] = $token;
                $response['status'] = 1;

                return response()->json($response, 200);
            }
            else{
                $response['msg'] = "Ha Ocurrido un Error";
                $response['status'] = 0;

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
#use GuzzleHttp\Exception\RequestException;
#use GuzzleHttp\Psr7\Request as GuzzleRequest;

class FileMakerApiRestController extends Controller
{


   public function __construct()
   {
   }

   public function login(Request $request)
   {
      try {
         if ($request->wantsJson()) {





         }else{
            return abort(404);
         }
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }

   public function logout()
   {

   }

   public function test($type)
   {
      try {
         switch ($type) {
            case 'connection':
               $this->test_connection();
               break;
         }
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }

   public function test_connection()
   {
      try {
         ;
         #Conectar con el cliente
         $client = new Client([
            'base_uri' => 'https://201.238.235.30/',
            'verify' => false
         ]);

         $login_uri = 'fmi/rest/api/auth/Tasks_FMAngular';

         $login_data = [
            'json' => [
               'user' => 'nuevo',
               'password' => '1234',
               'layout' => 'prueba'
            ]
         ];

         $response = $client->request('POST', $login_uri, $login_data);

         switch ($response->getStatusCode()) {
            case 200:

               $responseContents = json_decode($response->getBody()->getContents());
               #dd($responseContents->token);

               #Solicitar datos con el login
               $get_uri = 'fmi/rest/api/record/Tasks_FMAngular/prueba';

               $headers = [
                  'headers' => [
                     'Content-Type' => 'application/json',
                     'FM-Data-token' => $responseContents->token,
                  ]
               ];
               $res = $client->request('GET', $get_uri, $headers);

               dd(json_decode($res->getBody()->getContents()));
               #return response()->json(json_decode($res->getBody()->getContents()));

               break;

            case 401:
            case 402:
            case 403:
            case 404:
            case 405:
            case 422:
               dd('Error: '.$response->getStatusCode());
               break;
         }
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }


   /*
   public function index()
   {}

   public function create()
   {}

   public function store(Request $request)
   {}

   public function show($id)
   {}

   public function edit($id)
   {}

   public function update(Request $request, $id)
   {}

   public function destroy($id)
   {}
   */
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
#use GuzzleHttp\Exception\RequestException;
#use GuzzleHttp\Psr7\Request as GuzzleRequest;

class FileMakerApiRestController extends Controller
{
   private  $collection;
   private  $auth_data,
            $json_auth_data,
            $json_auth_usuarios_data,
            $service_data,
            $uri;

   public function __construct()
   {
      try {
         $this->collection = json_decode(json_encode(config('collection')));
         $this->auth_data = $this->collection->auth_data;
         $this->json_auth_data = $this->collection->json_auth_data;
         $this->json_auth_usuarios_data = $this->collection->json_auth_usuarios_data;
         $this->service_data = $this->collection->service_data;
         $this->uri = $this->collection->uri;
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }

   public function connect_api()
   {
      try {
         #Conectar con el cliente
         $client = new Client([
            'base_uri' => $this->uri->base_uri,
            'verify' => $this->service_data->verify,
         ]);

         $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);
         $response = $client->request('POST', $login_uri, $this->json_auth_data);
         return $response;

      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
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
   public function logout(Request $request)
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

   public function test($type, Request $request)
   {
      try {
         switch ($type) {
            case 'connection':
               return $this->test_connection($request);
               break;
         }
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }
   
   public function test_edit()
   {
      try {
         #$response = $this->connect_api();
         #Conectar con el cliente
         $client = new Client([
            'base_uri' => $this->uri->base_uri,
            'verify' => $this->service_data->verify,
         ]);

         $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);
         $response = $client->request('POST', $login_uri, $this->json_auth_data);

         switch ($response->getStatusCode()) {
            case 200:

               $responseContents = json_decode($response->getBody()->getContents());
               #dd($responseContents->token);

               #Enviar datos de modificacion
               $edit_uri = 'fmi/rest/api/record/Tasks_FMAngular/usuarios/1';

               $headers = [
                  'headers' => [
                     'Content-Type' => 'application/json',
                     'FM-Data-token' => $responseContents->token,
                  ]
               ];

               $data = [
                  'data' => [
                     'Us_Nombre' => 'Vitoco',
                     'Us_Apellido_P' => 'Garrafa',
                     'Us_Apellido_M' => 'Sep.',
                  ],
                  'modId' => '1'
               ];

               $response = $client->request('PUT', $edit_uri, $data);
               #$res = $client->request('GET', $edit_uri, $headers);

               #dd(json_decode($res->getBody()->getContents()));
               return response()->json(json_decode($response->getBody()->getContents()));

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

   public function test_connection(Request $request)
   {
      try {

         if ($request->wantsJson() || true) {
            #Conectar con el cliente
            $client = new Client([
               'base_uri' => $this->uri->base_uri,
               'verify' => $this->service_data->verify,
            ]);

            $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);

            #(array)$this->json_auth_data
            $response = $client->request('POST', $login_uri, (array)$this->json_auth_data);

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

                  $contents = json_decode($res->getBody()->getContents());

                  return response()->json($contents->data);

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

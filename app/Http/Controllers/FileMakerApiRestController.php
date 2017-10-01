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
            $uri,
            $client;

   public function __construct()
   {
      try {
         $this->collection = json_decode(json_encode(config('collection')));
         $this->auth_data = $this->collection->auth_data;
         $this->json_auth_data = $this->collection->json_auth_data;
         $this->json_auth_usuarios_data = $this->collection->json_auth_usuarios_data;
         $this->service_data = $this->collection->service_data;
         $this->uri = $this->collection->uri;

         #Conectar con el cliente
         $this->client = new Client([
            'base_uri' => $this->uri->base_uri,
            'verify' => $this->service_data->verify,
         ]);
      } catch (\Exception $ex) {
         return $ex->getMessage();
      }
   }

   /* Return response and FM-Data-token */
   public function connect_api($layout)
   {
      try {

         $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);

         $this->json_auth_data->json->layout = $layout;

         $response = $this->client->request('POST', $login_uri, (array)$this->json_auth_data);
         return $response;

      } catch (\Exception $ex) {return $ex->getMessage();}
   }

   public function getDataRequestByLayout ($layout) {

      $response = $this->connect_api($layout);

      $responseContents = json_decode($response->getBody()->getContents());

      #Solicitar datos con el login
      $get_uri = 'fmi/rest/api/record/Tasks_FMAngular/'.$layout;

      $headers = [
         'headers' => [
            'FM-Data-token' => $responseContents->token,
            'Content-Type' => 'application/json'
         ]
      ];

      $res = $this->client->request('GET', $get_uri, $headers);

      $contents = json_decode($res->getBody()->getContents());
   
      return response()->json($contents->data);
      
   }



   #Unused
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
   #Unused
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
      #try {
         switch ($type) {
            case 'connection':
               return $this->test_connection($request);
               break;
            case 'edit':
               return $this->test_edit($request);
               break;
         }
      #} catch (\Exception $ex) {
      #   return $ex->getMessage();
      #}
   }
   
   public function test_edit(Request $request)
   {
      #try {
         if ($request->wantsJson() || true) {
            #$response = $this->connect_api();
            #Conectar con el cliente
            $client = new Client([
               'base_uri' => $this->uri->base_uri,
               'verify' => $this->service_data->verify,
            ]);

            $login_uri = $this->uri->base_uri.str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);

            $response = $client->request('POST', $login_uri, (array)$this->json_auth_data);

            switch ($response->getStatusCode()) {
               case 200:

                  $responseContents = json_decode($response->getBody()->getContents());
                  #dd($responseContents->token);

                  #Enviar datos de modificacion
                  $edit_uri = $this->uri->base_uri.'fmi/rest/api/record/Tasks_FMAngular/usuarios/1';

                  $body = [
                     'form_params' => [
                        'Us_Nombre' => 'Vitoco',
                        'Us_Apellido_P' => 'Garrafa',
                        'Us_Apellido_M' => 'Sep.'
                     ],
                     'modId' => '2'
                  ];

                  $_body = [
                     'data' => [
                        'Us_Nombre' => 'Vitoco',
                        'Us_Apellido_P' => 'Garrafa',
                        'Us_Apellido_M' => 'Sep.'
                     ],
                  ];

                  $headers = [
                     'headers' => [
                        'FM-Data-token' => $responseContents->token,
                        'Content-Type' => 'application/json',
                     ],
                  ];

                  $options = [
                     'headers' => [
                        'FM-Data-token' => $responseContents->token,
                        'Content-Type' => 'application/json'
                     ],

                     'body' => [
                        'Us_Nombre' => 'Vitoco',
                        'Us_Apellido_P' => 'Garrafa',
                        'Us_Apellido_M' => 'Sep.'
                     ],
                  ];

                  #$data = json_decode(json_encode($data));
                  #$response = $client->request('PUT', $edit_uri, ['json'=>$data,'headers'=>$headers]);
                  $response = $client->put($edit_uri, [
                     'headers' => [
                        'FM-Data-token' => $responseContents->token,
                        'Content-Type' => 'application/json'
                     ],

                     'form_params' => (array)json_decode(json_encode([
                        'Us_Nombre' => 'Vitoco',
                        'Us_Apellido_P' => 'Garrafa',
                        'Us_Apellido_M' => 'Sep.'
                     ])),

                  ]);

                  dd($response);

                  dd(json_decode($response->getBody()->getContents()));

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
         }
      #} catch (\Exception $ex) {
      #   return $ex->getMessage();
      #}
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
                  $get_uri = 'fmi/rest/api/record/Tasks_FMAngular/usuarios';

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

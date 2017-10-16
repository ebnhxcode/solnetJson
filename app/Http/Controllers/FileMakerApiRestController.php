<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
#use GuzzleHttp\Exception\RequestException;
#use GuzzleHttp\Psr7\Request as GuzzleRequest;
class FileMakerApiRestController extends Controller
{
   private  $collection;#Coleccion de configuracion en config/collection.php
   private  $auth_data, #Datos de autenticacion con el api
            $json_auth_data, #Datos de autenticacion con el api en formato json
            $json_auth_usuarios_data, #Datos de autenticacion con el api de usuarios
            $service_data, #Datos de conecion con el servidor
            $uri, #Url del servidor ApiRest FM
            $client; #Objeto de instancia de la clase Guzzle
   public function __construct()
   {
      #Istancia de los objetos a disponer dentro de la API
      $this->collection = json_decode(json_encode(config('collection')));#Instancia de archivo php con configuracion
      $this->auth_data = $this->collection->auth_data; #Datos de autenticacion con el api
      $this->json_auth_data = $this->collection->json_auth_data;
      $this->json_auth_usuarios_data = $this->collection->json_auth_usuarios_data;
      $this->service_data = $this->collection->service_data;
      $this->uri = $this->collection->uri;
      #Conectar con el cliente
      $this->client = new Client([
         'base_uri' => $this->uri->base_uri,
         'verify' => $this->service_data->verify,
      ]);
   }
   /* Return response and FM-Data-token */
   public function connect_api($layout)
   {
      #Hace un replace de la keywork :solution por la solucion definida en la coleccion
      $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);
      #Asigna el layout al objeto para hacer la peticion a ese layout
      $this->json_auth_data->json->layout = $layout;
      #Guarda el response de la peticion y la retorna a la funcion que llamo la conexion
      $response = $this->client->request('POST', $login_uri, (array)$this->json_auth_data);
      return $response;
   }
   public function getDataRequestByLayout ($layout) {
      #Conecta con el Api de FM y recibe el response de la conexion
      $response = $this->connect_api($layout);
      #Decodifica el contenido de la respuesta del servidor, entre ellos el TOKEN
      $responseContents = json_decode($response->getBody()->getContents());
      #Solicitar datos con el login (concatena el layout a consultar)
      $get_uri = 'fmi/rest/api/record/Tasks_FMAngular/'.$layout;
      #Configura headers para hacer la peticion + token
      $headers = [
         'headers' => [
            'FM-Data-token' => $responseContents->token,
            'Content-Type' => 'application/json'
         ]
      ];
      #Hace la peticion a la Api de FM y envia como parametros la url y los headers
      $res = $this->client->request('GET', $get_uri, $headers);
      #Recibe el contenido y dispone en json para la aplicacion
      $contents = json_decode($res->getBody()->getContents());
      return response()->json($contents->data);
      
   }
   public function postDataRequestByLayout (Request $request) {
      $this->validate($request, [
         'user' => 'required',
         'pass' => 'required',
         'layout' => 'required',
      ]);
      #return response()->json($request->all());
      #return response()->json(['rc'=>'1']);
      if ($request->wantsJson()) {
         #dd(response()->json(['rc'=>'1']));
         #Conecta con el Api de FM y recibe el response de la conexion
         $response = $this->connect_api($request->layout);
         ##Al refactorizar validar por codigo 200 como condicion para controlar el error exception
         #Decodifica el contenido de la respuesta del servidor, entre ellos el TOKEN
         $responseContents = json_decode($response->getBody()->getContents());
         #return response()->json($responseContents);
         #Solicitar datos con el login (concatena el layout a consultar)
         $post_uri = 'fmi/rest/api/find/Tasks_FMAngular/'.$request->layout;
         #Configura headers para hacer la peticion + token
         $query = json_decode(json_encode([
            'query' => ['Us_Usuario' => '=Victor', 'Us_pass' => '=123']
         ]));
         $headers = [
            'headers' => [
               'Content-Type' => 'application/json',
               'FM-Data-token' => $responseContents->token
            ],
         ];
         $options = json_decode(json_encode([
            $query,
            $headers,
         ]));
         #(array) [ 'query' => ['Us_Usuario' => '=Victor', 'Us_pass' => '=123'] ]
         #Hace la peticion a la Api de FM y envia como parametros la url y los options (headers + body)
         $res = $this->client->post($post_uri, $headers, (array)json_decode(json_encode([ 'query' => [json_decode(json_encode(['Us_Usuario' => '=Victor', 'Us_pass' => '=123']))] ])) );
         #Recibe el contenido y dispone en json para la aplicacion
         $contents = json_decode($res->getBody()->getContents());
         return dd($contents);
         return response()->json($contents->data);
      }
      
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
      switch ($type) {
         case 'connection':
            return $this->test_connection($request);
            break;
         case 'edit':
            return $this->test_edit($request);
            break;
         case 'show':
            return $this->test_show($request);
            break;
      }
   }
   public function test_connection (Request $request)
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
   public function test_edit (Request $request)
   {
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
   }
   public function test_show (Request $request) {
      #dd(response()->json(['rc'=>'1']));
      #Conecta con el Api de FM y recibe el response de la conexion
      $response = $this->connect_api('usuarios');
      ##Al refactorizar validar por codigo 200 como condicion para controlar el error exception
      #Decodifica el contenido de la respuesta del servidor, entre ellos el TOKEN
      $responseContents = json_decode($response->getBody()->getContents());
      #return response()->json($responseContents);
      #Solicitar datos con el login (concatena el layout a consultar)
      $post_uri = 'fmi/rest/api/find/Tasks_FMAngular/usuarios';
      #Configura headers para hacer la peticion + token
      $query = json_decode(json_encode([
         'query' => ['Us_usuario' => 'Victor', 'Us_pass' => '123']
      ]));
      $headers = [
         'headers' => [
            'FM-Data-token' => $responseContents->token,
            'Content-Type' => 'application/json'
         ],
      ];
      $options = json_decode(json_encode([
         $headers,
         $query,
      ]));
      #(array) [ 'query' => ['Us_Usuario' => '=Victor', 'Us_pass' => '=123'] ]
      #Hace la peticion a la Api de FM y envia como parametros la url y los options (headers + body)
      $res = $this->client->post($post_uri, $headers, (array)json_decode(json_encode([ 'query' => [json_decode(json_encode(['Us_usuario' => 'Victor', 'Us_pass' => '123']))] ])) );
      #Recibe el contenido y dispone en json para la aplicacion
      $contents = json_decode($res->getBody()->getContents());
      return dd($contents);
      return response()->json($contents->data);
   }
}

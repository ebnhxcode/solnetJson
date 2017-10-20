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

   /*
    *  Instance all data that need to use */
   public function __construct () {
      #Istancia de los objetos a disponer dentro de la API
      $this->collection = json_decode(json_encode(config('collection')));#Instancia de archivo php con configuracion
      $this->auth_data = $this->collection->auth_data; #Datos de autenticacion con el api
      $this->json_auth_data = $this->collection->json_auth_data;
      $this->json_auth_usuarios_data = $this->collection->json_auth_usuarios_data;
      $this->service_data = $this->collection->service_data;
      $this->uri = $this->collection->uri;

      #Conectar guzzle con el cliente especificando los parametros
      $this->client = new Client([
         'base_uri' => $this->uri->base_uri,
         'verify' => $this->service_data->verify,
      ]);
   }

   /*
    *  Return response and FM-Data-token inside response */
   public function connect_api ($layout) {
      #Hace un replace de la keywork :solution por la solucion definida en la coleccion
      $login_uri = str_replace(':solution',$this->service_data->solution, $this->uri->login_uri);
      #Asigna el layout al objeto para hacer la peticion a ese layout
      $this->json_auth_data->json->layout = $layout;
      #Guarda el response de la peticion y la retorna a la funcion que llamo la conexion
      $response = $this->client->request('POST', $login_uri, (array)$this->json_auth_data);
      return $response;
   }

   public function disconnect_api () {
      $logout_uri = str_replace(':solution',$this->service_data->solution, $this->uri->logout_uri);
      $response = $this->client->request('POST', $logout_uri, (array)$this->json_auth_data);
      return dd($response);
   }

   /*
    *  Shortcut to connect with api, require $layout as parameter */
   public function login ($layout) { return $this->connect_api($layout); }

   public function logout (Request $request) { return $this->disconnect_api(); }


   public function delete (Request $request, $layout, $recordId) {
      $url = $this->uri->base_uri;
      $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->delete_uri);
      $url = str_replace(':layout',rawurlencode($layout), $url);
      $url = str_replace(':recordId',rawurlencode($recordId), $url);
      $payload = (array)$this->auth_data;
      $result = $this->curl($layout,'DELETE',$payload,$url);
      dd(json_decode($result));
   }

   public function create (Request $request, $layout) {
      $parameters = $request->all();

      $url = $this->uri->base_uri;
      $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->create_uri);
      $url = str_replace(':layout',rawurlencode($layout), $url);
      $payload = (array)$this->auth_data;
      $data = [
         'data' => [
            'Us_Nombre' => 'elliot',
            'Us_Apellido_P' => 'alderson',
            'Us_Apellido_M' => '.'
         ],
      ];
      $result = $this->curl($layout,'POST',$payload,$url,$data);
      dd(json_decode($result));
   }

   public function find (Request $request) {
      $layout = $request->layout;
      $url = $this->uri->base_uri;
      $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->find_uri);
      $url = str_replace(':layout',rawurlencode($layout), $url);
      $payload = (array)$this->auth_data;
      return response()->json($request->all());
      $query = [
         'query' => [['Us_Usuario' => '=Victor', 'Us_pass' => '=123']]
      ];
      $result = $this->curl($layout,'POST',$payload,$url,$query);
      return
      dd(json_decode($result));
   }

   public function edit (Request $request, $layout, $recordId) {
      if ($request->wantsJson() || true) {
         $data = [
            'data' => [
               'Us_Nombre' => 'Vitoco',
               'Us_Apellido_P' => 'Garrafalol',
               'Us_Apellido_M' => 'Sep.'
            ],
         ];

         $url = $this->uri->base_uri;
         $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->edit_uri);
         $url = str_replace(':layout',rawurlencode($layout), $url);
         $url = str_replace(':recordId',rawurlencode($recordId), $url);
         $payload = (array)$this->auth_data;
         $result = $this->curl($layout,'PUT',$payload,$url,$data);
         return json_decode($result);
         #dd(json_decode($result));
      }
   }

   public function get (Request $request, $layout, $recordId) {
      if ($request->wantsJson() || true) {
         $url = $this->uri->base_uri;
         $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->get_uri);
         $url = str_replace(':layout',rawurlencode($layout), $url);
         $url = str_replace(':recordId',rawurlencode($recordId), $url);
         $payload = (array)$this->auth_data;
         $result = $this->curl($layout,'GET',$payload,$url);
         return response()->json(json_decode($result));
         #dd(json_decode($result));
      }
   }

   public function all (Request $request, $layout) {
      if ($request->wantsJson() || true) {
         $url = $this->uri->base_uri;
         $url .= str_replace(':solution',rawurlencode($this->service_data->solution), $this->uri->get_all_uri);
         $url = str_replace(':layout',rawurlencode($layout), $url);
         $payload = (array)$this->auth_data;
         $result = $this->curl($layout,'GET',$payload,$url);
         return response()->json(json_decode($result));
         #dd(json_decode($result));
      }
   }

   public function curl ($layout, $method, $payload, $url, $query='') {
      $response = $this->login($layout);
      $responseContents = json_decode($response->getBody()->getContents());
      $token = $responseContents->token;

      if (is_array ($payload)) $payload = json_encode ($payload);
      if (is_array ($query)) $query = json_encode ($query);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);         //follow redirects
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         //return the transfer as a string
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);         //don't verify SSL CERT
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);         //don't verify SSL CERT
      curl_setopt($ch, CURLOPT_VERBOSE, true);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE); //Don'T use cache

      curl_setopt ($ch, CURLOPT_HTTPHEADER, array ('FM-Data-token:'. $token , 'Content-Type:application/json'));

      if (!empty ($payload)) {
         curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
         if (isset($query) && $query!='' && $query!=null && $query) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query );
         }
      }
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_URL, $url);
      $result = curl_exec($ch);
      $error = curl_error ($ch);
      $info = curl_getinfo ($ch);
      curl_close($ch);

      return $result;
   }


   public function test ($type, Request $request) {
      switch ($type) {
         case 'all':
            return $this->all($request, 'usuarios');
            break;
         case 'get':
            return $this->get($request, 'usuarios', 2);
            break;
         case 'edit_curl':
            return $this->edit($request, 'usuarios', 2);
            break;
         case 'find':
            return $this->find($request, 'usuarios');
            break;
         case 'create':
            return $this->create($request, 'usuarios');
            break;
         case 'delete':
            return $this->delete($request, 'usuarios', 10);
            break;
      }
   }
}

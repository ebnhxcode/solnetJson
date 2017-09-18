<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class TestController extends Controller
{

   public function __construct () {}

   public function test () {

      #Conectar con el cliente
      $client = new Client([
        'base_uri' => 'https://201.238.235.30/',
        'verify' => false
      ]);

      $uri = 'fmi/rest/api/auth/Tasks_FMAngular';

      $login_data = [
         'user' => 'nuevo',
         'password' => '1234',
         'layout' => 'prueba'
      ];

      $response = $client->request('POST', $uri, ['json' => $login_data]);
      
      switch ($response->getStatusCode()) {
         case 200:

            $responseContents = json_decode($response->getBody()->getContents());
            #dd($responseContents->token);

            #Solicitar datos con el login
            $uri_get = 'fmi/rest/api/record/Tasks_FMAngular/prueba';
            $headers = [
            #'Authorization' => 'Bearer ' . $responseContents->token,
            'Content-Type'        => 'application/json',
            'FM-Data-token' => $responseContents->token,
            ];
            $res = $client->request('GET', $uri_get, [
            'headers' => $headers
            ]);
            dd(json_decode($res->getBody()->getContents()));

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php
// config/constants.php

/*
   FileMaker Data API
   FileMaker Data API Reference

   Format for Data API URLs: /fmi/rest/api/:service/:solution/:layout/:recordId where
   :service is a pre-defined service keyword, such as auth, record, or find
   :solution is the name of a hosted FileMaker solution
   :layout is the name of the layout to be used as the context for the request
   :recordId is the unique ID number for a record
*/

return [
   #Post data auth
   'auth_data' => [
      'user'         => 'nuevo',
      'password'     => '1234',
      'layout'       => 'prueba'
   ],

   #Json post data auth
   'json_auth_data' => [
      'json' => [
         'user'      => 'nuevo',
         'password'  => '1234',
         'layout'    => 'prueba'
      ]
   ],

   #Json post data auth
   'json_auth_usuarios_data' => [
      'json' => [
         'user'      => 'nuevo',
         'password'  => '1234',
         'layout'    => 'usuarios'
      ]
   ],

   #Service project Solution and Layout
   'service_data' => [
      'solution'     => 'Tasks_FMAngular', #SOLUTION FOR CREATE/DELETE/UPDATE BY [POST,PUT,PATCH,DELETE]
      'layout'       => [
            'prueba'    => 'prueba',
            'usuarios'  => 'usuarios',
         ], #LAYOUT FOR CREATE/DELETE/UPDATE BY [POST,PUT,PATCH,DELETE]
      'verify'       => false, #OPTION TO VERIFY OR NOT SSL CERT
   ],

   #Uris
   'uri' => [
      'base_uri'     => 'https://201.238.235.30/', #URL BASE
      'login_uri'    => 'fmi/rest/api/auth/:solution', #POST TO GET SESSION TOKEN
      'logout_uri'   => 'fmi/rest/api/auth/:solution', #DELETE SESSION
      'create_uri'   => 'fmi/rest/api/record/:solution/:layout', #CREATE RECORD
      'delete_uri'   => 'fmi/rest/api/record/:solution/:layout/:recordId', #DELETE RECORD
      'edit_uri'     => 'fmi/rest/api/record/:solution/:layout/:recordId', #EDIT RECORD
      'get_uri'      => 'fmi/rest/api/record/:solution/:layout/:recordId', #GET A RECORD
      'get_all_uri'  => 'fmi/rest/api/record/:solution/:layout', #GET RECORDS
   ],

];

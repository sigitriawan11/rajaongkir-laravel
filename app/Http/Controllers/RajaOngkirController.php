<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RajaOngkirController extends Controller
{

    public function getProvince(){

        $province = Http::withHeaders([
            'key' => env("RAJA_ONGKIR")
        ])->get('https://api.rajaongkir.com/starter/province')['rajaongkir'];

        if($province['status']['code'] != 200){
            return response()->json([
                "status" => "400",
                "massage" => "Bad Request or ApiKey not found"
            ]);
        }

        return response()->json([
            "status" => "200",
            "message" => "Success",
            "results" => $province['results']
        ]);

    }

    public function getCity(Request $request){

        $city = Http::withHeaders([
            "key" => env("RAJA_ONGKIR")
        ])->get('https://api.rajaongkir.com/starter/city', [
            "province" => $request->province
        ])['rajaongkir'];


        if($city['status']['code'] != 200){
            return response()->json([
                "status" => "400",
                "massage" => "Bad Request or ApiKey not found"
            ]);
        }

        return response()->json([
            "status" => "200",
            "message" => "Success",
            "results" => $city['results']
        ]);

    }

    public function getOngkir(Request $request){

        $validate = Validator::make($request->all(), [
            "origin" => "required",
            "destination" => "required",
            "weight" => "required",
            "courier" => "required"
        ]);

        if($validate->fails()){
            return response()->json([
                "status" => "400",
                "message" => "Bad Request"
            ]);
        }

        $validated = $validate->validated();

        $ongkir = Http::withHeaders([
            "key" => env("RAJA_ONGKIR")
        ])->post('https://api.rajaongkir.com/starter/cost', [
            "origin" => $validated['origin'],
            "destination" => $validated['destination'],
            "weight" => $validated['weight'],
            "courier" => $validated['courier']
        ])['rajaongkir'];

        if($ongkir['status']['code'] != 200){
            return response()->json([
                "status" => "400",
                "massage" => "Bad Request or ApiKey not found"
            ]);
        }

        return response()->json([
            "status" => "200",
            "message" => "Success",
            "data" => [
                "origin_details" => $ongkir['origin_details'],
                "destination_details" => $ongkir['destination_details'],
            ],
            "results" => [
                 "courier_name" => $ongkir['results'][0]['name'],
                 "costs" => $ongkir['results'][0]['costs']
            ]
        ]);

    }
}

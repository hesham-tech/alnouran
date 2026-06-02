<?php

namespace App\Http\Controllers;

use App\Models\Preparation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreparationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $preparations = Preparation::all();

        return response()->json($preparations, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'ppm' => 'required|integer',
            'quantity' => 'required',
            'cont_hours' => 'required',
            'actual_time' => 'required',
            'slices_ton' => 'required',
            'shift' => 'required|string',
            'note' => 'nullable|string',
            'typePreparation_id' => 'nullable',
            'station_id' => 'nullable',
            'user_id' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $preparation = Preparation::create($request->all());

        return response()->json($preparation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $preparation = Preparation::find($id);
        
        if (!$preparation) {
            return response()->json(['message' => 'Preparation not found'], 404);
        }

        return response()->json($preparation, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $preparation = Preparation::find($id);

        if (!$preparation) {
            return response()->json(['message' => 'Preparation not found'], 404);
        }

        $rules = [
            'ppm' => 'required|integer',
            'quantity' => 'required',
            'cont_hours' => 'required',
            'actual_time' => 'required',
            'slices_ton' => 'required',
            'shift' => 'required|string',
            'note' => 'nullable|string',
            'typePreparation_id' => 'nullable',
            'station_id' => 'nullable',
            'user_id' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $preparation->update($request->all());

        return response()->json($preparation, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $preparation = Preparation::find($id);

        if (!$preparation) {
            return response()->json(['message' => 'Preparation not found'], 404);
        }

        $preparation->delete();

        return response()->json(['message' => 'Preparation deleted successfully'], 200);
    }
    }

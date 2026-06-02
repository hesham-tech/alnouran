<?php

namespace App\Http\Controllers;

use Exception;
use HTTP_Request2;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TypePreparation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class TypePreparationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = User::find($userId);
        /** @var User */
        $user = Auth::User();
        $stations = $user->stations;
        if ($stations->isEmpty()) {
            return ['لا يوجد محطات مرتبطة بالمستخدم'];
        }
        $relations = request()->relation;
        // 'latestPreparationActual.user'
        $stationIds = $stations->pluck('id');
        $typePreparations = TypePreparation::whereIn('station_id', $stationIds)->get();
        $typePreparations->load($relations);
        return $typePreparations;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:type_preparations|max:255',
            'description' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $typePreparation = TypePreparation::create($request->all());

        return response()->json($typePreparation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typePreparation = TypePreparation::find($id);

        if (!$typePreparation) {
            return response()->json(['message' => 'Type preparation not found'], 404);
        }

        return response()->json($typePreparation, 200);
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
        $typePreparation = TypePreparation::find($id);

        if (!$typePreparation) {
            return response()->json(['message' => 'Type preparation not found'], 404);
        }

        $rules = [
            'name' => 'required|string|unique:type_preparations,name,' . $id . '|max:255',
            'description' => 'required|string',
            'user_id' => 'required|unsignedBigInteger|exists:users,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $typePreparation->update($request->all());

        return response()->json($typePreparation, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $typePreparation = TypePreparation::find($id);

        if (!$typePreparation) {
            return response()->json(['message' => 'Type preparation not found'], 404);
        }

        $typePreparation->delete();

        return response()->json(['message' => 'Type preparation deleted successfully'], 200);
    }
    // public function FunctionName()
    // {
    //     require_once 'HTTP/Request2.php';
    //     $request = new HTTP_Request2();
    //     $request->setUrl('https://5yq14y.api.infobip.com/whatsapp/1/message/template')::METHOD_POST;
    //     $request->setConfig(array(
    //         'follow_redirects' => TRUE
    //     ));
    //     $request->setHeader(array(
    //         'Authorization' => 'App 8a8c7d5a996b0e662d85c9263eb9f9d6-b00764be-329b-42ce-8533-404fecca23a9',
    //         'Content-Type' => 'application/json',
    //         'Accept' => 'application/json'
    //     ));
    //     $request->setBody('{"messages":[{"from":"447860099299","to":"201007888009","messageId":"df6ba7b5-1331-4b2b-8a68-eca8dee09dc4","content":{"templateName":"message_test","templateData":{"body":{"placeholders":["هشام"]}},"language":"en"}}]}');
    //     try {
    //         $response = $request->send();
    //         if ($response->getStatus() == 200) {
    //             echo $response->getBody();
    //         } else {
    //             echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    //                 $response->getReasonPhrase();
    //         }
    //     } catch (HTTP_Request2_Exception $e) {
    //         echo 'Error: ' . $e->getMessage();
    //     }
    // }
}

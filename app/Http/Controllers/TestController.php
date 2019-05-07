<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test_library()
    {
        $url = "https://bgg-json.azurewebsites.net/collection/deadterrorist";
        $json = file_get_contents($url);
//        return response()->json($json);
        return $json;
    }

    public function hello ()
    {
        return Response()->json([
            'response' => true,
            'result' => 'Hello world'
        ], 200);
    }

    public function hello1 (Request $request)
    {
        //$test = $request->only("test");
        $test = $request->get("test", "fail");

        return response()->json([
            'response' => true,
            'result' => $test
        ]);
    }

    public function hello2 (Request $request)
    {
        //$test = $request->only("test");
        $test = $request->get("test", "fail");

        return Response()->json([
            'response' => true,
            'result' => $test
        ], 200);
    }
}

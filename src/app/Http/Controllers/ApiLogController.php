<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ApiLogController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(ApiLog::class, 'apiLog');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = ApiLog::orderBy("created_at", "desc")->get();
        return view("api_logs.index", compact("logs"));
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
     * @param  String  $apiLogId
     * @return \Illuminate\Http\Response
     */
    public function show($apiLogId)
    {
        $log = ApiLog::find($apiLogId);
        if($log->http_method == "POST"){
            $pushed_datas = $log->getPushedDatas();
            return view("api_logs.show", compact("log", "pushed_datas"));
        }

        return view("api_logs.show", compact("log"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ApiLog  $apiLog
     * @return \Illuminate\Http\Response
     */
    public function edit(ApiLog $apiLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ApiLog  $apiLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApiLog $apiLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ApiLog  $apiLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApiLog $apiLog)
    {
        //
    }
}

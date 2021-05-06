<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRefugeeRequest;
use App\Http\Requests\StoreRefugeeApiRequest;
use App\Http\Requests\StoreRefugeeRequest;
use App\Http\Requests\UpdateRefugeeRequest;
use App\Models\ApiLog;
use App\Models\Field;
use App\Models\Link;
use App\Models\Refugee;
use Illuminate\Http\Request;
use Illuminate\Http\RequestRefugeeRequest;
use Illuminate\Http\Response;


class ManageRefugeesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refugees = Refugee::where("deleted", 0)
            ->orderBy("date", "desc")
            ->get();

        return view("manage_refugees.index", compact("refugees"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $fields = Field::where("status", ">", 0)
            ->orderBy("required")
            ->orderBy("order")
            ->get();
        return view("manage_refugees.create", compact("fields"));
    }

    /**
     * Show the form for creating a new resource from a json file.
     *
     * @return Response
     */
    public function createFromJson()
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view("manage_refugees.create_from_json");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(StoreRefugeeRequest $request)
    {

        $refugee = $request->validated();

        $refugee["flight_disease"] = ((isset($refugee["flight_disease"]) && $refugee["flight_disease"] == "on") ? 1 : 0);
        $refugee["flight_boarded"] = ((isset($refugee["flight_boarded"]) && $refugee["flight_boarded"] == "on") ? 1 : 0);

        $new_ref = Refugee::create($refugee);

        return redirect()->route("manage_refugees.index");
    }

    /**
     * Store a newly created resource from json file in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function storeFromJson(FileRefugeeRequest $request)
    {
        foreach ($request->validated() as $refugee){
            Refugee::create($refugee);
        }
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Display the specified resource.
     *
     * @param String $id
     * @return Response
     */
    public function show(String $id)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $fields = Field::where("status", ">", 0)
            ->orderBy("required")
            ->orderBy("order")
            ->get();
        $refugee = Refugee::find($id);
        $links = Link::where("deleted",0)->where("from", $id)->orWhere("to", $id)->get();

        return view("manage_refugees.show", compact("refugee", "fields", "links"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param String $id
     * @return Response
     */
    public function edit(String  $id)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $refugee = Refugee::find($id);
        $fields = Field::where("status", ">", 0)
            ->orderBy("required")
            ->orderBy("order")
            ->get();
        return view("manage_refugees.edit", compact("refugee", "fields"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RequestRefugeeRequest $request
     * @param String $refugee_id
     * @return Response
     */
    public function update(UpdateRefugeeRequest $request, $refugee_id)
    {
        $refugee = $request->validated();
        $refugee["flight_disease"] = ((isset($refugee["flight_disease"]) && $refugee["flight_disease"] == "on") ? 1 : 0);
        $refugee["flight_boarded"] = ((isset($refugee["flight_boarded"]) && $refugee["flight_boarded"] == "on") ? 1 : 0);

        Refugee::find($refugee_id)
            ->update($refugee);
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $refugee_id
     * @return Response
     */
    public function destroy($refugee_id)
    {

        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Refugee::find($refugee_id)
            ->update(["deleted"=>1]);
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Handle the API request
     *
     * @param  StoreRefugeeApiRequest  $request
     * @return array
     */
    public static function handleApiRequest(StoreRefugeeApiRequest $request)
    {
        $log = ApiLog::createFromRequest($request, "Refugee");
        if($request->user()->tokenCan("update")){
            foreach ($request->validated() as $refugee) {
                $refugee["api_log"] = $log->id;
                $refugee["application_id"] = $log->application_id;

                $potential_refugee = Refugee::where("application_id", $refugee["application_id"])->where('unique_id', $refugee["unique_id"])->first();
                if ($potential_refugee != null) {
                    $potential_refugee->update($refugee);
                } else {
                    $stored_ref = Refugee::create($refugee);
                    if ($stored_ref == null) {
                        $log->update(["response" => "Error while creating a refugee"]);
                        return response("Error while creating this refugee :" . json_encode($refugee), 500);
                    }
                }
            }
           return response("Success !", 201);
        }
        $log->update(["response"=>"Bad token access"]);
        return response("Your token can't be use to send datas", 403);
    }
}

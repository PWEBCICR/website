<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Http\Requests\StoreRefugeeRequest;
use App\Http\Requests\UpdateRefugeeRequest;
use App\Http\Requests\StoreRefugeeApiRequest;
use App\Traits\Uuids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Refugee;
use Illuminate\Support\Facades\View;


class ManageRefugeesController extends Controller
{
    public function __construct()
    {
        /*
        $fields = Field::where("status", ">", 0)->get();
        $fields = DB::table('fields')
            ->orderBy('order')
            ->get();

        View::share('fields', $fields);*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refugees = Refugee::all();

        return view("manage_refugees.index", compact("refugees"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRefugeeRequest $request)
    {
        $new_ref = Refugee::create($request->validated());
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  String  $id
     * @return \Illuminate\Http\Response
     */
    public function show(String $id)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $fields = Field::where("status", ">", 0)
            ->orderBy("required")
            ->orderBy("order")
            ->get();
        $refugee = Refugee::find($id);
        return view("manage_refugees.show", compact("refugee", "fields"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  String  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(String  $id)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $refugee = Refugee::where("id", $id);
        return view("manage_refugees.edit", compact("refugee"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RequestRefugeeRequest $request
     * @param  Refugee $refugee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRefugeeRequest $request, Refugee $refugee)
    {
        $refugee->update($request->validated());
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Refugee $refugee)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $refugee->delete();
        return redirect()->route("manage_refugees.index");
    }

    /**
     * Get all the element from the model name.
     *
     * @param  Model  $model
     * @return array
     */
    public static function getLinkedList($model)
    {
        //abort_if(Gate::denies('manage_refugees_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $model = ucfirst($model);
        $model = 'App\Models\\'.$model;
        $list = $model::getLinkedList();//all()->toArray();
        return $list;
    }
    /**
     * Handle the API request
     *
     * @param  StoreRefugeeApiRequest  $request
     * @return array
     */
    public static function handleApiRequest(StoreRefugeeApiRequest $request)
    {
        if($request->user()->tokenCan("update")){
            foreach ($request->validated() as $refugee){
                Refugee::create($refugee);
            }
           return response("Success !", 200);
        }

        return response("Your token can't be use to send datas", 403);
    }
}

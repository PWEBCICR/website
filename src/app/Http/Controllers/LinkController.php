<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkApiRequest;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use App\Models\ListControl;
use App\Models\Refugee;
use App\Models\Relation;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = Link::where('deleted',0)->get();
        return view("links.index", compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lists["refugees"] = array_column(Refugee::where("deleted",0)->get()->toArray(), "full_name", "id");
        $lists["relations"] = array_column(Relation::where("deleted",0)->get()->toArray(), ListControl::where('name', "Relation")->first()->displayed_value, "id");
        return view("links.create", compact("lists"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLinkRequest $request)
    {
        Link::create($request->validated());
        return redirect()->route("links.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $link = Link::find($id);
        return view("links.show", compact("links"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $link = Link::find($id);
        $lists["relations"] = [$link->getRelationId() => $link->relation]+array_column(Relation::where("deleted",0)->get()->toArray(), ListControl::where('name', "Relation")->first()->displayed_value, "id");
        return view("links.edit", compact("link","lists"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLinkRequest $request, $id)
    {
        $link = Link::find($id);
        $link->update($request->validated());

        return redirect()->route("links.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Link::where("id",$id)
            ->update(["deleted"=>1]);
        return redirect()->route("links.index");
    }

    /**
     * Handle the API request
     *
     * @param  StoreLinkApiRequest  $request
     * @return array
     */
    public static function handleApiRequest(StoreLinkApiRequest $request)
    {
        if($request->user()->tokenCan("update")){
            foreach ($request->validated() as $link){
                $relation["from"] = Refugee::where("full_name", $link["from_full_name"])->where("unique_id", $link["from_unique_id"])->first()->id;
                $relation["to"] = Refugee::where("full_name", $link["to_full_name"])->where("unique_id", $link["to_unique_id"])->first()->id;
                $relation["relation"] = $link["relation"];
                $relation["detail"] = $link["detail"];

                $stored_link = Link::create($relation);
                if(!$stored_link->exists){
                    return response("Error while creating this refugee :".json_encode($link), 500);
                }
            }
            return response("Success !", 201);
        }

        return response("Your token can't be use to send datas", 403);
    }
}

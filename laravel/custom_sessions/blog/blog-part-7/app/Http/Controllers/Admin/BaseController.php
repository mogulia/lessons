<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $className = $this->modelClass;
        $model = $className::orderby('created_at', 'desc')->paginate(10);
        
        return view('admin.listing')
            ->with('pageTitle', $this->pageTitle)
            ->with('secondTitle', $this->secondTitle)
            ->with('columns', $this->columns)
            ->with('urlName', $this->urlName)
            ->with('model', $model);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create')
            ->with('pageTitle', $this->pageTitle)
            ->with('secondTitle', $this->secondTitle)
            ->with('createTitle', $this->createTitle)
            ->with('urlName', $this->urlName)
            ->with('fields', $this->fields);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $className = $this->modelClass;
        $model = new $className;
        
        foreach ($this->fields as $key => $field) {
            $model->$key = $request->get($key);
        }
        
        $model->save();
        
        return redirect($this->urlName);
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
        $className = $this->modelClass;
        $model = $className::findOrFail($id);
        
        return view('admin.update')
            ->with('pageTitle', $this->pageTitle)
            ->with('secondTitle', $this->secondTitle)
            ->with('updateTitle', $this->updateTitle)
            ->with('urlName', $this->urlName)
            ->with('fields', $this->fields)
            ->with('model', $model);
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
        $className = $this->modelClass;
        $model = $className::findOrFail($id);
        
        foreach ($this->fields as $key => $field) {
            if (isset($field['ignore_empty']) && $field['ignore_empty'] == true) {
                if ($request->get($key) == null || $request->get($key) == '') {
                    continue;
                }
            }
            $model->$key = $request->get($key);
        }
        
        // if ($model->validate()) {
            foreach ($this->fields as $key => $field) {
                if (isset($field['purgeable']) && $field['purgeable'] == true) {
                    unset($model->$key);
                }
                
                if (isset($field['hashable']) && $field['hashable'] == true) {
                    $model->$key = \Hash::make($model->$key);
                }
            }
            $model->save();
        // }
        
        return redirect($this->urlName);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $className = $this->modelClass;
        $model = $className::findOrFail($id);
        $model->delete();
        
        return redirect($this->urlName);
    }
}

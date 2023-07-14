<?php

namespace App\Http\Controllers\Disaster;

use App\Http\Controllers\Controller;
use App\Models\Disaster as DisasterModel;
use App\Http\Requests\CreateDisasterRequest;
use App\Http\Requests\UpdateDisasterRequest;
use App\Http\Requests\UpdateDisasterStatus;
use App\Http\Requests\UpdateDisasterStatusRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;

class DisasterController extends Controller
{

    public function index()
    {
        //$disasters = DisasterModel::all();
        $disasters = DisasterModel::orderBy('created_at', 'desc')->get();
        $total_disasters = $disasters->count();
        return response([
            'data' => $disasters,
            'total_disasters' => $total_disasters
        ], 200);
    }

    public function store(CreateDisasterRequest $request)
    {
        $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
        }

        $disasterData = [
            'title'=>$request->title,
            'date'=>$request->date,
            'disasterType'=>$request->disasterType,
            'location'=>$request->location,
            'information'=>$request->information,
            'filename' => $filename,
            'path' => $path,
        ];

        //authenticated->get the user id->assign to disaster user_id->create post
        auth()->user()->disasters()->create($disasterData);
        return response([
            'message' => 'The post was created successfully'
        ],201);
    }

    public function edit($id)
    {
        $disaster = DisasterModel::find($id);
        if($disaster){
            return response([
                'status'=>200,
                'data' => $disaster
            ], 200);
        }return response([
            'status'=>404,
            'message'=>'Disaster not found'
        ], 200);
    }

    public function update(UpdateDisasterRequest $request, $id)
    {
        $request->validated();
        $disaster = DisasterModel::findOrFail($id);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($disaster->path);
            
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
        }else {
            // Keep the existing image if no new image is uploaded
            $filename = $disaster->filename;
            $path = $disaster->path;
        }

        $disasterData = [
            'title'=>$request->title,
            'date'=>$request->date,
            'disasterType'=>$request->disasterType,
            'location'=>$request->location,
            'information'=>$request->information,
            'filename' => $filename,
            'path' => $path,
        ];

        //authenticated->get the user id->assign to disaster user_id->create post
        $disaster->update($disasterData);
        return response([
            'message' => 'The post was updated successfully'
        ],201);
    }
    
    public function delete($id)
    {
        $disaster = DisasterModel::find($id);
    
        if (!$disaster) {
            return response([
                'message' => 'Disaster not found'
            ], 404);
        }
    
        // Delete the associated image file
        if (Storage::disk('public')->exists($disaster->path)) {
            Storage::disk('public')->delete($disaster->path);
        }
    
        // Delete the disaster
        $disaster->delete();
    
        return response([
            'message' => 'The post was deleted successfully'
        ], 200);
    }

    public function getActive()
    {
        $disasters = DisasterModel::where('active', '=', '1'  )->orderBy('created_at', 'desc')->get();
        $total_disasters = $disasters->count();
        return response([
            'data' => $disasters,
            'active' => $total_disasters
        ], 200);
    }

    public function getInactive()
    {
        $disasters = DisasterModel::where('active', '=', '0'  )->orderBy('created_at', 'desc')->get();
        $total_disasters = $disasters->count();
        return response([
            'data' => $disasters,
            'inactive' => $total_disasters
        ], 200);
    }

    public function updateActive(UpdateDisasterStatusRequest $request, $id)
    {
        $request->validated();
        $disaster = DisasterModel::findOrFail($id);

        $disasterData = [
            'active' => $request->active,
        ];

        $disaster->update($disasterData);
        return response([
            'message' => 'The disaster was updated successfully'
        ],201);
    }
    
}

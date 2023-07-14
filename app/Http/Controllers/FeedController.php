<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFeedRequest;
use App\Http\Requests\UpdateFeedRequest;
use App\Models\Feed;
use Illuminate\Support\Facades\Storage;


class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::orderBy('created_at', 'desc')->get();
        $total_feeds = $feeds->count();
        return response([
            'data' => $feeds,
            'total_feeds' => $total_feeds
        ], 200);
    }

    public function store(CreateFeedRequest $request)
    {
        $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
        }

        $feedData = [
            'title'=>$request->title,
            'date'=>$request->date,
            'disasterType'=>$request->disasterType,
            'location'=>$request->location,
            'information'=>$request->information,
            'filename' => $filename,
            'path' => $path,
        ];

        //authenticated->get the user id->assign to disaster user_id->create post
        auth()->user()->feeds()->create($feedData);
        return response([
            'message' => 'The post was created successfully'
        ],201);
    }

    public function edit($id)
    {
        $feed = Feed::find($id);
        if($feed){
            return response([
                'status'=>200,
                'data' => $feed
            ], 200);
        }return response([
            'status'=>404,
            'message'=>'Feed not found'
        ], 200);
    }

    public function update(UpdateFeedRequest $request, $id)
    {
        $request->validated();
        $feed = Feed::findOrFail($id);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($feed->path);
            
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
        }else {
            // Keep the existing image if no new image is uploaded
            $filename = $feed->filename;
            $path = $feed->path;
        }

        $feedData = [
            'title'=>$request->title,
            'date'=>$request->date,
            'disasterType'=>$request->disasterType,
            'location'=>$request->location,
            'information'=>$request->information,
            'filename' => $filename,
            'path' => $path,
        ];

        //authenticated->get the user id->assign to disaster user_id->create post
        $feed->update($feedData);
        return response([
            'message' => 'The post was updated successfully'
        ],201);
    }
    
    public function delete($id)
    {
        $feed = Feed::find($id);
    
        if (!$feed) {
            return response([
                'message' => 'Disaster not found'
            ], 404);
        }
    
        // Delete the associated image file
        if (Storage::disk('public')->exists($feed->path)) {
            Storage::disk('public')->delete($feed->path);
        }
    
        // Delete the disaster
        $feed->delete();
    
        return response([
            'message' => 'The post was deleted successfully'
        ], 200);
    }
}

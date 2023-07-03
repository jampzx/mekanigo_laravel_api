<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Disaster;
use App\Models\Donation;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::orderBy('created_at', 'desc')->get();
        $total_donations = $donations->count();

        return response([
            'data' => $donations,
            'total_donations' => $total_donations
        ], 200);
    }

    public function store(CreateDonationRequest $request)
    {
        $request->validated();

        $donationData = [
            'name'=>$request->name,
            'age'=>$request->age,
            'contact_number'=>$request->contact_number,
            'email'=>$request->email,
            'donation_type'=>$request->donation_type,
            'donation_info' => $request->donation_info,
            'disaster_id' => $request->disaster_id
        ];

        //authenticated->get the user id->assign to donation user_id->create donation
        auth()->user()->donations()->create($donationData);
        return response([
            'message' => 'The post was created successfully'
        ],201);
    }

    public function edit($id)
    {
        $donation = Donation::find($id);
        if($donation){
            return response([
                'status'=>200,
                'data' => $donation
            ], 200);
        }return response([
            'status'=>404,
            'message'=>'Donation not found'
        ], 200);
    }

    public function update(UpdateDonationRequest $request, $id)
    {
        $request->validated();
        $donation = Donation::findOrFail($id);

        $donationData = [
            'verified' => $request->verified,
        ];

        $donation->update($donationData);
        return response([
            'message' => 'The donation was updated successfully'
        ],201);
    }

    public function delete($id)
    {
        $donation = Donation::find($id);
    
        if (!$donation) {
            return response([
                'message' => 'Donation not found'
            ], 404);
        }
        
        // Delete the disaster
        $donation->delete();
    
        return response([
            'message' => 'The donation was deleted successfully'
        ], 200);
    }

    public function getDonationPerUser($id)
    {
        $user = User::findOrFail($id);
        $donationPerUser = Donation::where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->get();

        return response([
            'data' => $user,
            'donation_of_user' => $donationPerUser
        ], 200);
    }

    public function getDonationPerDisaster($id)
    {
        $disaster = Disaster::findOrFail($id);
        $donationPerDisaster = Donation::where('disaster_id', '=', $disaster->id)->get();
        $totalDonations = $donationPerDisaster->count();
        return response([
            'data' => $donationPerDisaster,
            'total_donations' => $totalDonations
        ], 200);
    }
}

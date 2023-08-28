<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)->where('status', 'upcoming')->get();
        $reviews = Reviews::where('shop_id', $shop->id)->where('status', 'active')->get();

        //if successfully, return status code 200
        return response([
            'shop' => $shop,
            'appointments'=> $appointments,
            'reviews' => $reviews
        ], 200);

    }

    public function store(Request $request)
    {
        //this controller is to store booking details post from mobile app
        $reviews = new Reviews();
        //this is to update the appointment status from "upcoming" to "complete"
        $appointment = Appointment::where('id', $request->get('appointment_id'))->first();

        if (!$appointment) {
            return response()->json([
                'error' => 'The appointment does not exist.',
            ], 404);
        }

        //save the ratings and reviews from user
        $reviews->user_id = Auth::user()->id;
        $reviews->shop_id = $request->get('shop_id');
        $reviews->ratings = $request->get('ratings');
        $reviews->reviews = $request->get('reviews');
        $reviews->reviewed_by = Auth::user()->name;
        $reviews->status = 'active';
        $reviews->save();

        //change appointment status
        $appointment->status = 'complete';
        $appointment->save();

        return response()->json([
            'success'=>'The appointment has been completed and reviewed successfully!',
        ], 200);
    }
}

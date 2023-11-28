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

    public function profile()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();

        //if successfully, return status code 200
        return response([
            'shop' => $shop,
        ], 200);

    }

    public function reviews()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $reviews = Reviews::where('shop_id', $shop->id)->where('status', 'active')->get();

        //if successfully, return status code 200
        return response([
            'reviews' => $reviews
        ], 200);

    }


    public function getAllCompleted()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)->where('status', 'completed')->orderBy('type', 'asc')->orderBy('created_at', 'asc')->get();

        //if successfully, return status code 200
        return response([
            'appointments'=> $appointments,
        ], 200);

    }

    public function getEmergencyAppointments()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)->where('status', 'upcoming')->where('type', 'Emergency')->get();

        //if successfully, return status code 200
        return response([
            'appointments'=> $appointments,
        ], 200);

    }

    public function getAllPending()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)->where('status', 'pending')->orderBy('type', 'asc')->orderBy('created_at', 'asc')->get();

        //if successfully, return status code 200
        return response([
            'appointments'=> $appointments,
        ], 200);

    }

    public function getAllUpcoming()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)->where('status', 'upcoming')->orderBy('type', 'asc')->orderBy('created_at', 'asc')->get();

        //if successfully, return status code 200
        return response([
            'appointments'=> $appointments,
        ], 200);

    }

    public function getCompletedRejected()
    {
        //get shops's appointment, clients, reviews and display on dashboard
        $shop = Auth::user();
        $appointments = Appointment::where('shop_id', $shop->id)
        ->where(function($query) {
            $query->where('status', 'completed')
                  ->orWhere('status', 'rejected');
        })
        ->orderBy('type', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();
    
        //if successfully, return status code 200
        return response([
            'appointments'=> $appointments,
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
        $appointment->status = 'completed';
        $appointment->save();

        return response()->json([
            'success'=>'The appointment has been completed and reviewed successfully!',
        ], 200);
    }

    public function acceptAppointment(Request $request)
    {

        //this is to update the appointment status from "upcoming" to "complete"
        $appointment = Appointment::where('id', $request->get('appointment_id'))->first();

        if (!$appointment) {
            return response()->json([
                'error' => 'The appointment does not exist.',
            ], 404);
        }

        //change appointment status
        $appointment->status = 'upcoming';
        $appointment->save();

        return response()->json([
            'success'=>'The appointment has been accepted successfully!',
        ], 200);
    }

    public function cancelAppointment(Request $request)
    {

        //this is to update the appointment status from "upcoming" to "complete"
        $appointment = Appointment::where('id', $request->get('appointment_id'))->first();

        if (!$appointment) {
            return response()->json([
                'error' => 'The appointment does not exist.',
            ], 404);
        }

        //change appointment status
        $appointment->status = 'rejected';
        $appointment->save();

        return response()->json([
            'success'=>'The appointment has been rejected successfully!',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAppointmentRequest;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        //retrieve all appointments from the user
        $appointment = Appointment::where('user_id', Auth::user()->id)->get();
        $shop = User::where('user_type', 'shop')->get();

        //sorting appointment and shop details
        //and get all related appointment
        foreach($appointment as $data){
            foreach($shop as $info){
                if($data['shop_id'] == $info['id']){
                    $data['shop_name'] = $info['name'];
                    $data['shop_profile'] = $info['path']; //typo error found
                }
            }
        }

        return $appointment;
    }

    public function store(CreateAppointmentRequest $request)
    {
        //this controller is to store booking details post from mobile app
        $appointment = new Appointment();
        $appointment->user_id = Auth::user()->id;
        $appointment->shop_id = $request->get('shop_id');
        $appointment->shop_id = $request->get('shop_latitude');
        $appointment->shop_id = $request->get('shop_longitude');
        $appointment->date = $request->get('date');
        $appointment->day = $request->get('day');
        $appointment->time = $request->get('time');
        $appointment->status = 'upcoming'; //new appointment will be saved as 'upcoming' by default
        $appointment->save();

        //if successfully, return status code 200
        return response()->json([
            'success'=>'New Appointment has been made successfully!',
        ], 200);

    }
}

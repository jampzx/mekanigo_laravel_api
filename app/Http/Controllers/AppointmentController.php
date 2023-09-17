<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAppointmentRequest;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    //Get appointments of authenticated user
    public function index()
    {
        // Retrieve all appointments from the user
        $appointments = Appointment::where('user_id', Auth::user()->id)->orderBy('date', 'desc')->orderBy('time', 'desc')->get();
        $shops = User::where('user_type', 'shop')->get();
    
        // Sorting appointment and shop details and get all related appointments
        foreach ($appointments as $appointment) {
            foreach ($shops as $shop) {
                if ($appointment->shop_id == $shop->id) { // Use "->" instead of "['']" to access object properties
                    $appointment->shop_name = $shop->name; // Assign shop name to the appointment object
                    $appointment->shop_profile = $shop->path; // Corrected the field name
                    $appointment->shop_address = $shop->address;
                }
            }
        }
    
        return response()->json([
            'data' => $appointments,
            'success' => 'Appointments retrieved successfully!',
        ], 200);
    }
    

    //create appointment
    public function store(CreateAppointmentRequest $request)
    {
        // This controller is to store booking details posted from the mobile app
        $appointment = new Appointment();
        $appointment->user_id = Auth::user()->id;
        $appointment->shop_id = $request->get('shop_id');
        $appointment->shop_latitude = $request->get('shop_latitude'); // Corrected field name
        $appointment->shop_longitude = $request->get('shop_longitude'); // Corrected field name
        $appointment->date = $request->get('date');
        // $appointment->day = $request->get('day');
        $appointment->time = $request->get('time');
        $appointment->status = 'upcoming'; // New appointments will be saved as 'upcoming' by default
        $appointment->name = $request->get('name');
        $appointment->contact_number = $request->get('contact_number');
        $appointment->email = $request->get('email');
        $appointment->address = $request->get('address');
        $appointment->type = $request->get('type');
        $appointment->save();
    
        // If successful, return status code 200
        return response()->json([
            'success' => 'New Appointment has been made successfully!',
        ], 200);
    }
    
}

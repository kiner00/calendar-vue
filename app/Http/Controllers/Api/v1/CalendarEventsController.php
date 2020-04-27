<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CalendarEventResource;
use App\CalendarEvent;

class CalendarEventsController extends Controller
{
    public function index(): CalendarEventResource
    {
        return new CalendarEventResource(CalendarEvent::get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name'    =>  'required',
            'date'    =>  'required',
            'days'    =>  'required',
        ]);

        $startDate = $request->date[0];
        $endDate = $request->date[1];

        $endDate = strtotime($endDate);

        $dates = [];
        if(in_array(1, $request->days)){
            for($i = strtotime('Monday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(2, $request->days)){
            for($i = strtotime('Tuesday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(3, $request->days)){
            for($i = strtotime('Wednesday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(4, $request->days)){
            for($i = strtotime('Thursday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(5, $request->days)){
            for($i = strtotime('Friday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(6, $request->days)){
            for($i = strtotime('Saturday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }
        if(in_array(7, $request->days)){
            for($i = strtotime('Sunday', strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)){
                $dates[] = date('Y-m-d', $i);
            }
        }

        CalendarEvent::truncate();
        foreach($dates as $date){
            $calendarEvent = new CalendarEvent;
            $calendarEvent->event_name = $request->event_name;
            $calendarEvent->date = $date;
            $calendarEvent->save();
        }

        return response()->json(["message" => "Sucessfuly added Event"]);
    }
}

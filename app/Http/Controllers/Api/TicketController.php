<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    use ApiResponse;
    public function store(Request $request, $eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return $this->errorResponse('Event not found', 404);
        }

        $user = $request->user();

        DB::beginTransaction();

        try {
            $event = Event::where('id', $eventId)->lockForUpdate()->firstOrFail();

            $exisitingTicket = Ticket::where( 'user_id', $user->id)->where('event_id', $event->id)->where('is_canceled', false)->exists();

            if ($exisitingTicket) {
                DB::rollBack();
                return $this->errorResponse('You have already reserved a ticket for this event', 400);
            }

            $currentBookings = $event->tickets()->where('is_canceled', false)->count();

            if ($currentBookings >= $event->max_reservation) {
                DB::rollBack();
                return $this->errorResponse('Event is fully booked', 400);
            }

            $payload = [
                'un' => $user->name, // user name
                'ue' => $user->email, // user email
                'en' => $event->name, // event name
                'ed' => $event->date, // event date
            ];

            $encode = base64_encode(json_encode($payload));
            // ikutan-xxxxx-payload
            $code = 'ikutan-' . uniqid() . '-' . $encode;

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'code' => $code,
            ]);

            DB::commit();
            return $this->successResponse(['ticket' => $ticket], 'Ticket reserved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function indexByUser(Request $request)
    {
        $user = $request->user();

        $tickets = $user->tickets()->latest()->get();

        return $this->successResponse($tickets, 'Tickets fetched successfully', 200);
    }
    public function indexByEvent(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return $this->errorResponse('Event not found', 404);
        }

        $tickets = $event->tickets()->where('is_canceled', false)->latest()->get();
        return $this->successResponse($tickets, 'Tickets fetched successfully', 200);
    }
}

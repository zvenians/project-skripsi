<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApprovedBookingCalendar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(ApprovedBookingCalendar $calendar)
    {
        return $this->renderCalendar('all', $calendar);
    }

    public function search(Request $request, ApprovedBookingCalendar $calendar)
    {
        $validated = $request->validate(['organisasi' => ['required', Rule::in(array_merge(['all'], User::query()->pluck('id')->map(fn ($id) => (string) $id)->all()))]]);
        return $this->renderCalendar((string) $validated['organisasi'], $calendar);
    }

    private function renderCalendar(string $id_user, ApprovedBookingCalendar $calendar)
    {
        return view('jadwal', [
            'id_user' => $id_user,
            'user' => User::query()->orderBy('name')->get(['id', 'name']),
            'calendarEvents' => $calendar->events($id_user),
        ]);
    }
}

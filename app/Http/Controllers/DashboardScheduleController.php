<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class DashboardScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\App\Services\ApprovedBookingCalendar $calendar)
    {
        return $this->renderCalendar(auth()->user()->is_admin ? 'all' : (string) auth()->id(), $calendar);
    }

    public function cari(Request $request, \App\Services\ApprovedBookingCalendar $calendar)
    {
        abort_unless(auth()->user()->is_admin, 403);
        $request->validate(['organisasi' => 'required']);
        $selection = (string) $request->input('organisasi');
        if ($selection !== 'all') {
            $request->validate(['organisasi' => 'exists:users,id']);
        }
        return $this->renderCalendar($selection, $calendar);
    }

    private function renderCalendar(string $selection, \App\Services\ApprovedBookingCalendar $calendar)
    {
        return view('dashboard.schedule.index', [
            'id_user' => $selection,
            'user' => User::query()->orderBy('name')->get(['id', 'name']),
            'id' => auth()->user()->is_admin,
            'calendarEvents' => $calendar->events($selection),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::get();
        $facility = Facility::get();
        $schedule = Schedule::get();

        return view('dashboard/schedule/create', compact('facility','user','schedule'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'time_s'    =>  'required',
            'selesai'   =>  'required'
        ]);
            Schedule::create([
            'user_id' => $request->input('user_id'),
            'day' => $request->input('day'),
            'time_s' => $request->input('time_s'),
            'facility_id' => $request->input('facility_id'),
            'time_end'  =>  $request->input('selesai'),
        ]);
        return redirect('dashboard/schedule')->with('sucess','schedule berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(schedule $schedule)
    {
        $user = User::get();
        $facility = Facility::get();
        // $schedule = schedule::get();

        return view('dashboard/schedule/edit', compact('user','facility','schedule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, schedule $schedule)
    {
        $request->validate([
            'time_s'    =>  'required',
            'selesai'   =>  'required'
        ]);
        $schedule = Schedule::whereId($schedule->id)->update([
            'user_id' => $request->input('user_id'),
            'day' => $request->input('day'),
            'time_s' => $request->input('time_s'),
            'facility_id' => $request->input('facility_id'),
            'time_end'  =>  $request->selesai
        ]);
        return redirect('dashboard/schedule')->with('sucess', 'schedule telah disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(schedule $schedule)
    {
        $schedule = Schedule::find($schedule->id);
        $schedule->delete();

        return redirect('dashboard/schedule');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tour;
use App\Models\Visit;
use App\Models\Components;
use App\Models\TourSchedule;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class TourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('publicShow');
        $this->middleware('permission:create-tour|edit-tour|delete-tour', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-tour', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-tour', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-tour', ['only' => ['destroy']]);
    }

    public function index(): View
    {
        $tours = Tour::with(['components', 'volunteers'])->orderBy('start_date', 'desc')->paginate(10);
        return view('tours.index', compact('tours'));
    }

    public function create(): View
    {
        $components = Components::all();
        $volunteers = User::role('Volunteer junior')->get();
        return view('tours.create', compact('components', 'volunteers'));
    }

    public function store(StoreTourRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $tour = Tour::create(Arr::except($validatedData, ['components', 'volunteer_id', 'schedules']));

        if (!empty($validatedData['components'])) {
            $tour->components()->sync($validatedData['components']);
        }

        if (!empty($validatedData['volunteer_id'])) {
            $tour->volunteers()->attach($validatedData['volunteer_id']);
        }

        if (!empty($validatedData['schedules'])) {
            foreach ($validatedData['schedules'] as $schedule) {
                $tour->schedules()->create($schedule);
            }
        }

        return redirect()->route('tours.index')->with('success', 'Tour creado con éxito!');
    }

    public function show(Tour $tour)
    {
        return view('tours.show', compact('tour'));
    }

    public function publicShow(): View
    {
        $today = Carbon::now()->toDateString();

        $tours = Tour::with(['components', 'schedules', 'volunteers'])
            ->get()
            ->filter(function ($tour) use ($today) {
                $visibility_start_date = $this->calculateVisibilityStartDate($tour->start_date, $tour->visibility_period);
                return $today >= $visibility_start_date && $today <= $tour->start_date;
            });

        foreach ($tours as $tour) {
            if ($tour->components->isNotEmpty() && $tour->components->first()->rutaImagenComponente) {
                $randomComponentWithImage = $tour->components->whereNotNull('rutaImagenComponente')->random();
                $tour->randomImage = $randomComponentWithImage->rutaImagenComponente;
            } else {
                $tour->randomImage = null;
            }

            $tour->available = false;
            foreach ($tour->schedules as $schedule) {
                $reservedSeats = Visit::where('tour_schedule_id', $schedule->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->sum('number_of_people');
                $schedule->available_seats = $schedule->max_capacity - $reservedSeats;

                if ($schedule->available_seats > 0) {
                    $tour->available = true;
                }
            }
        }

        return view('tour', compact('tours'));
    }

    private function calculateVisibilityStartDate($startDate, $visibilityPeriod)
    {
        $startDate = Carbon::parse($startDate);

        switch ($visibilityPeriod) {
            case '1 día':
                return $startDate->copy()->subDay();
            case '2 días':
                return $startDate->copy()->subDays(2);
            case '1 semana':
                return $startDate->copy()->subWeek();
            case '2 semanas':
                return $startDate->copy()->subWeeks(2);
            case '1 mes':
                return $startDate->copy()->subMonth();
            case '2 meses':
                return $startDate->copy()->subMonths(2);
            case '3 meses':
                return $startDate->copy()->subMonths(3);
            default:
                return $startDate;
        }
    }

    public function edit(Tour $tour): View
    {
        $components = Components::all();
        $volunteers = User::role(['Volunteer junior', 'Volunteer senior'])->get();
        $assignedVolunteer = $tour->volunteers->first();

        return view('tours.edit', compact('tour', 'components', 'volunteers', 'assignedVolunteer'));
    }

    public function update(UpdateTourRequest $request, Tour $tour): RedirectResponse
    {
        $validatedData = $request->validated();

        $tour->update(Arr::except($validatedData, ['components', 'volunteer_id', 'schedules']));

        if (array_key_exists('components', $validatedData)) {
            $tour->components()->sync($validatedData['components']);
        }

        if (!empty($validatedData['volunteer_id'])) {
            $tour->volunteers()->sync([$validatedData['volunteer_id']]);
            $volunteer = User::find($validatedData['volunteer_id']);
            $tour->update(['contact_info' => $volunteer->phone]);
        }

        // Actualiza los horarios del tour
        if (!empty($validatedData['schedules'])) {
            $tour->schedules()->delete(); // Elimina los horarios existentes

            foreach ($validatedData['schedules'] as $schedule) {
                $tour->schedules()->create($schedule); // Crea los nuevos horarios
            }
        }

        return redirect()->route('tours.index')->with('success', 'Tour actualizado con éxito!');
    }



    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()->route('tours.index')
            ->withSuccess('Tour eliminado con éxito.');
    }
}

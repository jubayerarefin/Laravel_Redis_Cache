<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $validatedData = $request->validate([
            'month' => ['numeric', 'max:12', 'min:1'],
            'year' => ['numeric', 'max:2019', 'min:1900'],
        ]);

        $people = Cache::tags([$validatedData['month'], $validatedData['year']])->get('people', null);

        if ($people === null) {
            $query = DB::table('people');

            if ($validatedData['month']) {
                $query->whereMonth('dob', $validatedData['month']);
            }

            if ($validatedData['year']) {
                $query->whereYear('dob', $validatedData['year']);
            }

            $people = $query->get();

            Cache::tags([$validatedData['month'], $validatedData['year']])->put('people', $people, 60);
        }

        $people = collect($people)->paginate(20)->withQueryString();;

        return view('people.index', compact('people'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return Application|Factory|View
     */
    public function index(\Illuminate\Http\Request $request): View|Factory|Application
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

        return view('people.index', compact('people'));
    }
}

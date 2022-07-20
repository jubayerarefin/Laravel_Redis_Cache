<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Predis;

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
        try {
            //Validates the request data
            $validatedData = $request->validate([
                'month' => ['numeric', 'max:12', 'min:1'],
                'year' => ['numeric', 'max:2019', 'min:1900'],
            ]);

            //Pull data from the cache if it exists
            $people = Cache::tags([$validatedData['month'], $validatedData['year']])->get('people', null);

            //If the data is not in the cache, pull it from the database
            if ($people === null) {
                //Clear cache is the filter parameters are changed
                Cache::flush();

                $query = DB::table('people');

                if ($validatedData['month']) {
                    $query->whereMonth('dob', $validatedData['month']);
                }

                if ($validatedData['year']) {
                    $query->whereYear('dob', $validatedData['year']);
                }

                $people = $query->get();

                //Cache all fetched data for the next request
                Cache::tags([$validatedData['month'], $validatedData['year']])->put('people', $people, 60);
            }
            //Test Begins
//            $query = DB::table('people');
//
//            if ($validatedData['month']) {
//                $query->whereMonth('dob', $validatedData['month']);
//            }
//
//            if ($validatedData['year']) {
//                $query->whereYear('dob', $validatedData['year']);
//            }
//
//            $client = new Predis\Client('tcp://localhost:6379');
//            $query->get()
//                ->map(function ($person) use ($client) {
//                    $client->zadd("people", [json_encode($person) => $person->id]);
//                    return (array)$person;
//                })->toArray();
//
//            $people = $client->zscan('people', 1);
//
//            $people = collect($people)->map(function ($person) {
//                return json_decode($person);
//            });
//            dd($people);
//            dump($people);

            //Test Ends

            $people = collect($people)->paginate(20)->withQueryString();

            return view('people.index', compact('people'));
        } catch (\Exception $e) {
//            $client = new Predis\Client('tcp://localhost:6379');
//            $people = $client->zscan('people', 0);
            dump($e->getMessage());
//            return view('people.index', compact('people'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Predis;

class PersonController extends Controller
{
    private Predis\Client $client;
    private Collection $people;

    public function __construct()
    {
        $this->client = new Predis\Client('tcp://localhost:6379');
    }

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
            $validatedData = Validator::make($request->route()->parameters(), [
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer|between:1900,2099',
            ])->validate();

            /*
             * Uses Sorted Set to store the data in the cache for a period of time.
             */
            if ($this->client->zcard('people') > 0) {
                $this->people = $this->getCachedData('people', 0, -1);
            } else {
                $query = DB::table('people');

                if ($validatedData['month']) {
                    $query->whereMonth('dob', $validatedData['month']);
                }

                if ($validatedData['year']) {
                    $query->whereYear('dob', $validatedData['year']);
                }

                $this->people = $query->get()
                    ->map(function ($person) {
                        $this->client->zadd("people", [json_encode($person) => $person->id]);
                        return (array)$person;
                    });
                $this->client->expire('people', '60');
                $this->people = $this->getCachedData('people', 0, -1);
            }
            //Test Ends

            $people = $this->people->paginate(20)->withQueryString();

            return view('people.index', compact('people'));
        } catch (\Exception $e) {
            return view('people.index', compact($e->getMessage()));
        }
    }

    /**
     * @param String $key
     * @param Int $start
     * @param Int $end
     * @return Collection
     */
    private function getCachedData(string $key, int $start, int $end): Collection
    {
        $sorted_set = $this->client->zrange($key, $start, $end);
        return collect($sorted_set)->map(function ($person) {
            return json_decode($person);
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Predis;

class PersonController extends Controller
{
    private Predis\Client $client;

    public function __construct()
    {
        $this->client = new Predis\Client('tcp://localhost:6379');
    }

    public function index(Request $request)
    {
        try {
            //Validates the request data
            $validatedData = Validator::make($request->all(), [
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer|between:1900,2019',
            ])->validate();

            $month = Arr::has($validatedData, 'month') ? $validatedData['month'] : null;
            $year = Arr::has($validatedData, 'year') ? $validatedData['year'] : null;
            if ($month == null && $year == null) {
                return redirect()->route('people', ['month' => 1, 'year' => 1900]);
            }
            $key = 'M' . $month . '-Y' . $year;

            //Uses Sorted Set to store the data in the cache for a period of time.
            if ($this->client->zcard($key) <= 0) {
                $this->client->flushall();
                $query = DB::table('people');

                if ($month) {
                    $query->whereMonth('dob', $month);
                }

                if ($year) {
                    $query->whereYear('dob', $year);
                }

                $query->get()
                    ->map(function ($person) use ($key) {
                        $this->client->zadd($key, [json_encode($person) => $person->id]);
                        return (array)$person;
                    });
                $this->client->expire($key, '60');
            }

            $cachedData = $this->getCachedData($key, 0, -1);

            $paginatedData = $cachedData->paginate(20)->withQueryString();

            return view('people.index', compact('paginatedData'));
        } catch (\Exception $e) {
            dump($e->getMessage(), $e->getLine());
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

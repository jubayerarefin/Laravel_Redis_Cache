<?php

namespace App\Observers;

use App\Models\Person;
use Illuminate\Support\Facades\Cache;

class PersonObserver
{
    /**
     * Handle the Person "created" event.
     *
     * @param Person $person
     * @return void
     */
    public function created(Person $person): void
    {
        Cache::flush();
    }

    /**
     * Handle the Person "updated" event.
     *
     * @param Person $person
     * @return void
     */
    public function updated(Person $person): void
    {
        Cache::flush();
    }

    /**
     * Handle the Person "deleted" event.
     *
     * @param Person $person
     * @return void
     */
    public function deleted(Person $person): void
    {
        Cache::flush();
    }

    /**
     * Handle the Person "restored" event.
     *
     * @param Person $person
     * @return void
     */
    public function restored(Person $person): void
    {
        Cache::flush();
    }

    /**
     * Handle the Person "force deleted" event.
     *
     * @param Person $person
     * @return void
     */
    public function forceDeleted(Person $person): void
    {
        Cache::flush();
    }
}

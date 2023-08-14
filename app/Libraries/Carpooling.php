<?php
namespace App\Libraries;

use App\Models\Car;
use App\Models\Journey;
use Illuminate\Support\Facades\DB;

/**
 * Class Carpooling
 * 
 * This class is responsible for managing all the carpooling logic
 */
class Carpooling {

    /**
     * @param Journey $journey
     * 
     * Checks if there's people waiting for a car to become available, and tries to accomodate them
     */
    public function checkAwaiting($car)
    {
        $journeys_that_fit = Journey::whereNull('car_id')->where('people', '<=', $car->empty_seats)->orderBy('rowid')->get();

        foreach ($journeys_that_fit as $journey)
        {
            if ($car->empty_seats >= $journey->people)
            {
                DB::beginTransaction();

                $car->empty_seats -= $journey->people;
                $car->save();
    
                $journey->car_id = $car->id;
                $journey->save();
    
                DB::commit();

                if ($car->empty_seats == 0)
                {
                    break;
                }
            }            
        }
    }

    /**
     * @param Journey $journey
     * @return bool
     * 
     * Tries to find a car that would accomodate this group straight away.
     * Returns TRUE if it finds a car, and FALSE if they have to wait
     */
    public function tryToAccomodate(Journey $journey)
    {
        // get the car with the fewest amount of free seats that would accomodate this group
        $car = Car::where('empty_seats', '>=', $journey->people)->orderBy('empty_seats')->first();

        if ($car)
        {
            DB::beginTransaction();

            $car->empty_seats -= $journey->people;
            $car->save();

            $journey->car_id = $car->id;
            $journey->save();

            DB::commit();

            return TRUE;
        }

        return FALSE;
    }
}
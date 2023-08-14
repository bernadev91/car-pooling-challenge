<?php

namespace App\Models;

use App\Libraries\Carpooling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Journey extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['id', 'car_id', 'people'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Method that handles when a group of people is dropped off
     */
    public function dropOff()
    {
        DB::beginTransaction();

        $car = NULL;
        if ($this->car)
        {
            $car = $this->car;
            $car->empty_seats += $this->people;
            $car->save(); 
        }

        $this->delete();

        DB::commit();

        if ($car)
        {
            // check if there's people waiting
            $carpooling = new Carpooling;
            $carpooling->checkAwaiting($car);
        }
    }
    /**
     * @param array $input
     * 
     * Inserts a new group into the database. It checks if the group would fit in one of the 
     * cars of the fleet.
     * 
     * Returns FALSE if there's a problem during the process and TRUE if the group was added to the
     * queue successfully
     */
    public static function processRequest($input)
    {
        if (!empty($input))
        {
            // check if there's a vehicle that would fit this group
            $largest_vehicle = Cache::get('largest_vehicle') ?? 0;

            if (!$largest_vehicle || $largest_vehicle < $input['people'])
            {
                return FALSE;
            }

            try
            {
                $journey = new Journey([
                    'id' => (int) $input['id'],
                    'people' => (int) $input['people'],
                ]);

                $journey->save();
            }
            catch (\Illuminate\Database\QueryException $ex)
            {
                return FALSE;
            }
            
            // check if there's a car available to start the journey straight away
            $carpooling = new Carpooling;
            $carpooling->tryToAccomodate($journey);

            return TRUE;
        }
       
        return FALSE;
    }
}
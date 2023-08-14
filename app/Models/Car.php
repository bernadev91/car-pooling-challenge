<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Car extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['id', 'seats', 'empty_seats'];

    /**
     * @param array $cars
     * 
     * Method that manages the input of the fleet of cars available
     * It truncates all the tables first to reset the application state
     */
    public static function processRequest($cars)
    {
        if (!empty($cars))
        {
            DB::beginTransaction();

            Journey::truncate();
            Car::truncate();

            $largest_vehicle = 0;

            try
            {
                foreach ($cars as $car)
                {
                    $car_model = new Car([
                        'id' => (int) $car['id'],
                        'seats' => (int) $car['seats'],
                        'empty_seats' => (int) $car['seats']
                    ]);

                    if ($car_model->seats > $largest_vehicle)
                    {
                        $largest_vehicle = $car_model->seats;
                    }

                    $car_model->save();
                }

                Cache::put('largest_vehicle', $largest_vehicle);
            }
            catch (\Illuminate\Database\QueryException $ex)
            {
                DB::rollBack();
                return FALSE;
            }

            DB::commit();

            return TRUE;
        }

        return FALSE;
    }
}

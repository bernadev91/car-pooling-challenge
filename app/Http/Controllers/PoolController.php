<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Journey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Controller to manage the actions related to the carpooling application.
 * The only work that this controller does is to receive the requests, pass them to the models in the right format,
 * and then prepare the output in the correct for the API.
 */
class PoolController extends Controller
{
    function status()
    {
        return response('', 200);
    }
    function cars(Request $request)
    {
        if ($request->isJson())
        {
            $success = Car::processRequest($request->json()->all());

            if ($success)
            {
                return response('', 200);
            }
        }

        return response('', 400);
    }
    function journey(Request $request)
    {
        if ($request->isJson())
        {
            $success = Journey::processRequest($request->json()->all());

            if ($success)
            {
                return response('', 200);
            }
        }

        return response('', 400);
    }
    function dropoff(Request $request)
    {
        if ($request->input('ID'))
        {
            try
            {
                $journey = Journey::findOrFail($request->input('ID'));
                $journey->dropOff();

                return response('', 200);
            }
            catch (ModelNotFoundException $ex)
            {
                return response('', 404);
            }
        }
        else
        {
            return response('', 400);
        }
    }
    function locate(Request $request)
    {
        if ($request->input('ID'))
        {
            try
            {
                $journey = Journey::with('car')->findOrFail($request->input('ID'));

                if ($journey->car)
                {
                    return response()->json($journey->car);
                }
                else
                {
                    return response('', 204);
                }
            }
            catch (ModelNotFoundException $ex)
            {
                return response('', 404);
            }
        }
        else
        {
            return response('', 400);
        }
    }
}

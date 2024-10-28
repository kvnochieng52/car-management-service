<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Events\CarCreated;
use App\Events\CarUpdated;
use App\Events\CarDeleted;
use App\Services\UserService;

class CarController extends Controller
{

    public function getAllCars(Request $request)
    {

        try {
            $cars = Car::all();
            return response()->json([
                'success' => true,
                'data' => $cars,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cars.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createCar(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric',
            'availability_status' => 'required|boolean',
        ]);
        try {


            $user = $request->get('authenticatedUser');
            $car = Car::create([
                'name' => $request->name,
                'model' => $request->model,
                'price' => $request->price,
                'availability_status' => $request->availability_status,
                'created_by' =>  $user['data']['id'],
                'updated_by' =>  $user['data']['id'],
            ]);

            // Dispatch the CarCreated event
            event(new CarCreated($car));

            return response()->json([
                'success' => true,
                'data' => $car->fresh(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create car.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCar(Request $request, $id)
    {

        try {
            $car = Car::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $car,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve car.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateCar(Request $request, $id)
    {

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'availability_status' => 'sometimes|required|boolean',
        ]);

        try {
            $user = $request->get('authenticatedUser');
            $car = Car::findOrFail($id);
            $car->update(array_merge(
                $request->all(),
                ['updated_by' => $user['data']['id']]
            ));


            event(new CarUpdated($car));

            return response()->json([
                'success' => true,
                'data' => $car->fresh(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update car.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCar(Request $request, $id)
    {

        try {
            $car = Car::findOrFail($id);
            $car->delete();

            event(new CarDeleted($id));

            return response()->json([
                'success' => true,
                'message' => 'Car deleted successfully.',
            ], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete car.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

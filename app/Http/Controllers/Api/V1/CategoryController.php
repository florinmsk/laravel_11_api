<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Method for getting all categories
    public function index()
    {
        try {
            // Retrieve all categories from the database
            $categories = Category::all();

            // Check if no categories are found
            if ($categories->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No categories found.',
                ], 404); // Return a 404 status if no categories are found
            }

            // Return the categories with a success status (200)
            return response()->json([
                'status' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => $categories,
            ], 200); // Return a 200 status for a successful response

        } catch (QueryException $exception) {
            // Catch any database-related errors
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500); // Return a 500 status for server errors

        } catch (\Exception $exception) {
            // Catch any other unexpected errors
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500); // Return a 500 status for internal server errors
        }
    }

    // Method for getting a single category by ID
    public function show($id)
    {
        try {
            // Find the category by ID, or return a 404 if not found
            $category = Category::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Category retrieved successfully.',
                'data' => $category,
            ], 200); // Return status 200 for success

        } catch (QueryException $exception) {
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500); // Return status 500 for database errors

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500); // Return status 500 for unexpected errors
        }
    }

    // Method for creating a new category
    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create the category
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category successfully created.',
                'data' => $category,
            ], 201); // Return status 201 for created resource

        } catch (QueryException $exception) {
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500);
        }
    }

    // Method for updating an existing category
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find the category by ID
            $category = Category::findOrFail($id);

            // Update the category
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category successfully updated.',
                'data' => $category,
            ], 200); // Return status 200 for success

        } catch (QueryException $exception) {
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500);
        }
    }

    // Method for deleting a category
    public function destroy($id)
    {
        try {
            // Find the category by ID
            $category = Category::findOrFail($id);

            // Soft delete the category (mark it as deleted)
            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Category successfully deleted.',
            ], 200); // Return status 200 for success

        } catch (QueryException $exception) {
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500); // Return status 500 for database errors

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500); // Return status 500 for unexpected errors
        }
    }
}

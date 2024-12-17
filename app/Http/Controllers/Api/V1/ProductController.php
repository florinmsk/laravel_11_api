<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Method for getting all products
    public function index()
    {
        try {
            // Retrieve all products, including soft deleted ones if needed
            $products = Product::all();

            // Check if no products are found
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No products found.',
                ], 404); // Return a 404 status if no products are found
            }

            // Return the products with a success status (200)
            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully.',
                'data' => $products,
            ], 200);

        } catch (QueryException $exception) {
            // Catch any database-related errors
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $exception->getMessage(),
            ], 500);
        } catch (\Exception $exception) {
            // Catch any other unexpected errors
            return response()->json([
                'status' => false,
                'error' => 'An unexpected error occurred: ' . $exception->getMessage(),
            ], 500);
        }
    }

    // Method for getting a single product by ID
    public function show($id)
    {
        try {
            // Find the product by ID, or return a 404 if not found
            $product = Product::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Product retrieved successfully.',
                'data' => $product,
            ], 200);

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

    // Method for creating a new product
    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'image' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create the product
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->image,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product successfully created.',
                'data' => $product,
            ], 201);

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

    // Method for updating an existing product
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'image' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find the product by ID
            $product = Product::findOrFail($id);

            // Update the product
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->image,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product successfully updated.',
                'data' => $product,
            ], 200);

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

    // Method for soft deleting a product
    public function destroy($id)
    {
        try {
            // Find the product by ID
            $product = Product::findOrFail($id);

            // Perform soft delete
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product successfully soft deleted.',
            ], 200);

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
}

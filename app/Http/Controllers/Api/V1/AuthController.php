<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Models\User;

class AuthController extends Controller
{

    // Method for registering a new user
    public function register(Request $request) {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255', 
            'first_name' => 'required|string|max:255', 
            'email' => 'required|string|email|max:255|unique:users,email', 
            'password' => 'required|string|min:8|confirmed',
        ], [
            // Custom error message for password confirmation validation failure
            'last_name.required' => 'The last name is required.',
            'first_name.required' => 'The first name is required.',
            'email.required' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'The passwords do not match.',
            'email.unique' => 'This email address is already registered.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
        // Create a new user record in the database
        $user = User::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a plain text authentication token for the newly created user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return a JSON response with the created user's details and the authentication token
        return response()->json([
            'status' => true, 
            'message' => 'User successfully created.',
            'user' => [
                'id' => $user->id, 
                'first_name' => $user->first_name, 
                'last_name' => $user->last_name, 
                'email' => $user->email, 
            ],
            'access_token' => $token, 
        ], 201); // Return a 201 status code            
        } catch (QueryExceptio $exception) {
            return response()->json(['error' => 'Database error: ' . $exception->getMessage()], 500);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    // Method for logging in an existing user
    public function login(Request $request) {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email', 
            'password' => 'required|string|min:8',
        ]);

        // If validation fails, return the validation errors with a 422 status code
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }     

        // Prepare the credentials for authentication
        $credentials = ['email' => $request->email, 'password' => $request->password];
        
        try {
            // Attempt to authenticate the user with the provided credentials
            if (!auth()->attempt($credentials)) {
                // If authentication fails, return a more descriptive error message with a 401 status code
                return response()->json(['error' => 'Invalid credentials. Please check your email and password.'], 401);
            }

            // If authentication is successful, get the user details from the database
            $user = User::where('email', $request->email)->firstOrFail();

            // Update the last login timestamp for the authenticated user
            $user->last_login_at = now();
            $user->save();

            // Generate a plain text authentication token for the authenticated user
            $token = $user->createToken('auth_token')->plainTextToken;            

            // Return a JSON response with the authenticated user's details and the authentication token
            return response()->json([
                'status' => true, 
                'message' => 'User successfully logged in.',
                'user' => [
                    'id' => $user->id, 
                    'first_name' => $user->first_name, 
                    'last_name' => $user->last_name, 
                    'email' => $user->email, 
                    'avatar' => $user->avatar, 
                    'role' => $user->role, 
                    'email_verified_at' => $user->email_verified_at, 
                    'last_login_at' => $user->last_login_at, 
                    'created_at' => $user->created_at, 
                    'updated_at' => $user->updated_at, 
                ],
                'access_token' => $token, 
            ], 200); // Return a 200 status code for successful login             

        } catch (\Exception $th) {
            // If an exception occurs, return the error message with a 500 status code
            return response()->json(['error' => 'Server error: ' . $th->getMessage()], 500);
        }
    }

    // Method for logging out the authenticated user
    public function logout(Request $request) {

        // Check if the user is authenticated
        $user = $request->user();

        // If the user is not authenticated, return an error message with a 401 status code
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        // Delete only the current token associated with the user (logging out from the current device)
        $request->user()->currentAccessToken()->delete();

        // Return a JSON response indicating the user has successfully logged out
        return response()->json([
            'status' => true, 
            'message' => 'User successfully logged out from the current device.',
        ], 200); // Return a 200 status code for successful logout
    }

    // Method for logging out the authenticated user from all devices
    public function logoutFromAllDevices(Request $request) {

        // Check if the user is authenticated
        $user = $request->user();

        // If the user is not authenticated, return an error message with a 401 status code
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        // Delete all tokens associated with the user (logging the user out from all devices)
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        // Return a JSON response indicating the user has successfully logged out from all devices
        return response()->json([
            'status' => true, 
            'message' => 'User successfully logged out from all devices.',
        ], 200); // Return a 200 status code for successful logout from all devices
    }
    
}

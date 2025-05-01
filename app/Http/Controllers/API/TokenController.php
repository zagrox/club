<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokenController extends Controller
{
    /**
     * Get all tokens for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens;
        
        return response()->json([
            'tokens' => $tokens
        ]);
    }
    
    /**
     * Create a new token for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'abilities' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $token = $request->user()->createToken(
            $request->name,
            $request->abilities ?? ['*']
        );
        
        return response()->json([
            'token' => $token->plainTextToken,
            'message' => 'Token created successfully'
        ]);
    }
    
    /**
     * Delete a specific token
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        $request->user()->tokens()->where('id', $id)->delete();
        
        return response()->json([
            'message' => 'Token deleted successfully'
        ]);
    }
    
    /**
     * Delete all tokens for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAll(Request $request)
    {
        // Delete all tokens except the current one
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
        
        return response()->json([
            'message' => 'All tokens deleted successfully'
        ]);
    }
} 
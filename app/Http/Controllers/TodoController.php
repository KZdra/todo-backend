<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;
use Exception;

class TodoController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Display a listing of the todos for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodos()
    {
        try {
            $userId = Auth::id();
            $todos = DB::table('todos')->where('user_id', $userId)->get();

            return $this->successResponse($todos, 'Todos retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve todos');
        }
    }

    /**
     * Store a newly created todo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'activity' => 'required|string|max:255',
        ]);

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $todoId = DB::table('todos')->insertGetId([
                'user_id' => $userId,
                'activity' => $request->activity,
                'done' => false,
                'created_at' => now(),
            ]);

            $newTodo = DB::table('todos')->where('id', $todoId)->first();

            DB::commit();

            return $this->todoAddedResponse($newTodo);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create todo');
        }
    }

    /**
     * Update the specified todo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'done' => 'required|boolean',
        ]);

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $updated = DB::table('todos')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update([
                    'done' => $request->done,
                    'updated_at' => now(),
                ]);

            DB::commit();

            if ($updated) {
                return $this->successResponse(null, 'Todo updated successfully');
            } else {
                return $this->errorResponse('Failed to update todo or todo not found');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update todo');
        }
    }

    /**
     * Remove the specified todo from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $deleted = DB::table('todos')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->delete();

            DB::commit();

            if ($deleted) {
                return $this->successResponse(null, 'Todo deleted successfully');
            } else {
                return $this->errorResponse('Failed to delete todo or todo not found');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete todo');
        }
    }
}

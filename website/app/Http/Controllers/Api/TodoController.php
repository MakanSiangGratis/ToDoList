<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToDo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    public function toDos(Request $request)
    {
        // Ambil semua todos user
        $todos = ToDo::where('user_id', $request->user()->id)->get();
        
        // Update status untuk setiap todo berdasarkan deadline
        foreach ($todos as $todo) {
            if ($todo->status !== 'completed' && $todo->deadline && Carbon::parse($todo->deadline)->isPast()) {
                $todo->update(['status' => 'late']);
            }
        }
        
        // Ambil ulang data setelah update
        $todos = ToDo::where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $todos
        ], 200);
    }

    function postTodo(Request $request){
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string', // Ubah ke nullable
                'deadline' => 'required|date|after_or_equal:today',
            ]);

            // Tentukan status berdasarkan deadline
            $deadline = Carbon::parse($request->deadline);
            $status = 'pending';
            
            if ($deadline->isPast()) {
                $status = 'late';
            }

            $todo = new ToDo();
            $todo->user_id = $request->user()->id;
            $todo->title = $request->title;
            $todo->description = $request->description;
            $todo->deadline = $request->deadline;
            $todo->status = $status;
            $todo->save();

            return response()->json([
                'status' => 200,
                'message' => 'Todo added successfully',
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to add todo: ' . $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    public function updateToDo(Request $request, $id){
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'deadline' => 'required|date',
                'status' => 'nullable|in:pending,in_progress,completed,late',
            ]);

            $todo = ToDo::find($id);
            if (!$todo) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Todo not found',
                    'data' => null
                ], 404);
            }

            if ($todo->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 403);
            }

            // Tentukan status berdasarkan deadline jika tidak ada status yang dikirim
            $status = $request->status ?? $todo->status;
            
            // Jika status bukan completed dan deadline sudah lewat, set ke late
            if ($status !== 'completed') {
                $deadline = Carbon::parse($request->deadline);
                if ($deadline->isPast()) {
                    $status = 'late';
                }
            }

            $todo->title = $request->title;
            $todo->description = $request->description;
            $todo->deadline = $request->deadline;
            $todo->status = $status;
            $todo->save();

            return response()->json([
                'status' => 200,
                'message' => 'Todo updated successfully',
                'data' => $todo
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update todo: ' . $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    // Method baru untuk update status saja
    public function updateStatus(Request $request, $id){
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed,late',
            ]);

            $todo = ToDo::find($id);
            if (!$todo) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Todo not found',
                    'data' => null
                ], 404);
            }

            if ($todo->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 403);
            }

            $todo->status = $request->status;
            
            // Jika status berubah menjadi completed, set completed_at
            if ($request->status === 'completed') {
                $todo->completed_at = Carbon::now();
            } else {
                $todo->completed_at = null;
            }
            
            $todo->save();

            return response()->json([
                'status' => 200,
                'message' => 'Status updated successfully',
                'data' => $todo
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update status: ' . $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    public function deleteToDo(Request $request, $id){
        try {
            $todo = ToDo::find($id);
            if (!$todo) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Todo not found',
                    'data' => null
                ], 404);
            }

            if ($todo->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 403);
            }

            $todo->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Todo deleted successfully',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to delete todo: ' . $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    public function updateLateStatus(Request $request){
        try{
            $updatedCount = ToDo::where('user_id', $request->user()->id)
                ->where('status', '!=', 'completed')
                ->whereDate('deadline', '<', Carbon::today())
                ->update(['status' => 'late']);

            return response()->json([
                'status' => 200,
                'message' => "Updated {$updatedCount} todos to late status",
                'data' => null
            ], 200);
        } catch( \Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update late status: ' . $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
}
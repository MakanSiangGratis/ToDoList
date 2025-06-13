<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TodoController extends Controller
{
    public function index(){
        $todos = Todo::where('user_id', Auth::id())
                    ->orderBy('deadline', 'asc')
                    ->get();

        // Update status for overdue todos
        foreach($todos as $todo) {
            if($todo->deadline && Carbon::parse($todo->deadline)->isPast() && $todo->status === 'pending') {
                $todo->update(['status' => 'late']);
            }
        }

        $stats = [
            'total' => $todos->count(),
            'completed' => $todos->where('status', 'completed')->count(),
            'pending' => $todos->where('status', 'pending')->count(),
            'late' => $todos->where('status', 'late')->count(),
        ];

        return view('index', compact('todos', 'stats'));
    }

    public function create(){
        return view('todos.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'deadline' => 'nullable|date|after_or_equal:today',
        ], [
            'title.required' => 'Judul todo wajib diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'deadline.date' => 'Format tanggal tidak valid',
            'deadline.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini',
        ]);

        Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', 'Todo berhasil ditambahkan!');
    }

    public function edit(Todo $todo){
        // Pastikan user hanya bisa edit todo miliknya
        if($todo->user_id !== Auth::id()){
            abort(403);
        }

        return view('todos.edit', compact('todo'));
    }

    public function update(Request $request, Todo $todo){
        // Pastikan user hanya bisa update todo miliknya
        if($todo->user_id !== Auth::id()){
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'deadline' => 'nullable|date',
            'status' => 'required|in:pending,late,completed',
        ], [
            'title.required' => 'Judul todo wajib diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'deadline.date' => 'Format tanggal tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ];

        // Set completed_at when status is completed
        if($request->status === 'completed') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        $todo->update($updateData);

        return redirect()->route('home')->with('success', 'Todo berhasil diperbarui!');
    }

    public function destroy(Todo $todo){
        // Pastikan user hanya bisa hapus todo miliknya
        if($todo->user_id !== Auth::id()){
            abort(403);
        }

        $todo->delete();
        return redirect()->route('home')->with('success', 'Todo berhasil dihapus!');
    }

    public function toggleStatus(Todo $todo){
        // Pastikan user hanya bisa toggle todo miliknya
        if($todo->user_id !== Auth::id()){
            abort(403);
        }

        $newStatus = $todo->status === 'completed' ? 'pending' : 'completed';
        $updateData = ['status' => $newStatus];

        if($newStatus === 'completed') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        $todo->update($updateData);

        return redirect()->route('home')->with('success', 'Status todo berhasil diperbarui!');
    }
}

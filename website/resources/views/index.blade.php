<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#667eea',
                        secondary: '#764ba2',
                        accent: '#f093fb',
                        dark: '#1a202c',
                        light: '#f7fafc',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-in': 'bounceIn 0.8s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    backdropBlur: {
                        xs: '2px',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 min-h-screen font-inter">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-accent/20 to-primary/20 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-secondary/10 to-accent/10 rounded-full blur-2xl animate-pulse-slow"></div>
    </div>

    <!-- Header -->
    <header class="relative bg-white/80 backdrop-blur-lg shadow-lg border-b border-white/20 sticky top-0 z-50">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-secondary/5"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-2xl blur opacity-75"></div>
                        <div class="relative bg-gradient-to-r from-primary to-secondary p-3 rounded-2xl">
                            <i class="fas fa-tasks text-white text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Todo List
                        </h1>
                        <p class="text-sm text-gray-500 font-medium">Organize your life</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Welcome back,</p>
                            <p class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" class="group relative bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-sign-out-alt mr-2 group-hover:rotate-12 transition-transform duration-300"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-400 to-green-500 text-white px-6 py-4 rounded-2xl mb-8 flex items-center shadow-lg animate-slide-up">
                <div class="bg-white/20 p-2 rounded-full mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">Success!</p>
                    <p class="text-green-100">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl p-8 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 animate-fade-in">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                        <i class="fas fa-list text-white text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Tasks</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl p-8 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Completed</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>

            <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl p-8 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 shadow-lg">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl p-8 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 animate-fade-in" style="animation-delay: 0.3s;">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center">
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Late</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['late'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
            <div>
                <h2 class="text-4xl font-black text-gray-900 mb-2">My Tasks</h2>
                <p class="text-gray-600 font-medium">Manage and track your daily activities</p>
            </div>
            <a href="{{ route('todos.create') }}" class="group relative bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-accent text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center space-x-3">
                <div class="bg-white/20 p-2 rounded-full group-hover:rotate-180 transition-transform duration-500">
                    <i class="fas fa-plus text-sm"></i>
                </div>
                <span>Add New Task</span>
            </a>
        </div>

        <!-- Todo List -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            @if($todos->count() > 0)
                @php
                    // Separate completed and incomplete tasks
                    $incompleteTodos = $todos->where('status', '!=', 'completed');
                    $completedTodos = $todos->where('status', 'completed');
                @endphp

                <div class="divide-y divide-gray-100/50">
                    <!-- Active/Incomplete Tasks Section -->
                    @if($incompleteTodos->count() > 0)
                        @foreach($incompleteTodos as $index => $todo)
                            <div class="group p-8 hover:bg-gradient-to-r hover:from-gray-50/50 hover:to-blue-50/30 transition-all duration-300 animate-slide-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-6 flex-1">
                                        <!-- Status Toggle -->
                                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="group/btn relative w-8 h-8 rounded-full border-3 border-gray-300 hover:border-primary transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-primary/20">
                                                <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-full opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                                <i class="fas fa-check text-white text-sm opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300 relative z-10"></i>
                                            </button>
                                        </form>

                                        <!-- Todo Content -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors duration-300">
                                                        {{ $todo->title }}
                                                    </h3>
                                                    
                                                    @if($todo->description)
                                                        <p class="text-gray-600 mb-4 leading-relaxed">
                                                            {{ $todo->description }}
                                                        </p>
                                                    @endif

                                                    <div class="flex items-center space-x-4">
                                                        <!-- Status Badge -->
                                                        @if($todo->status === 'late')
                                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
                                                                <i class="fas fa-exclamation-triangle mr-2"></i>Late
                                                            </span>
                                                        @else
                                                                                                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg">
                                                                <i class="fas fa-clock mr-2"></i>Pending
                                                            </span>
                                                        @endif

                                                        <!-- Deadline -->
                                                        @if($todo->deadline)
                                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                                                                {{ \Carbon\Carbon::parse($todo->deadline)->format('M d, Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-3 ml-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                                        <a href="{{ route('todos.edit', $todo) }}" 
                                           class="group/edit relative p-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                           title="Edit task">
                                            <i class="fas fa-edit group-hover/edit:rotate-12 transition-transform duration-300"></i>
                                        </a>
                                        <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="group/delete relative p-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all duration-300 hover:scale-110 hover:shadow-lg">
                                                <i class="fas fa-trash group-hover/delete:rotate-12 transition-transform duration-300"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Completed Tasks Section -->
                    @if($completedTodos->count() > 0)
                        <!-- Separator for completed tasks -->
                        @if($incompleteTodos->count() > 0)
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-t-4 border-gradient-to-r from-green-400 to-emerald-500">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-2 rounded-full mr-4">
                                            <i class="fas fa-check-circle text-white"></i>
                                        </div>
                                        <span>Completed Tasks</span>
                                        <span class="ml-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                            {{ $completedTodos->count() }}
                                        </span>
                                    </h3>
                                    <button onclick="toggleCompletedTasks()" id="toggleCompletedBtn" class="group flex items-center space-x-2 text-gray-600 hover:text-gray-800 font-semibold transition-colors duration-300">
                                        <span id="toggleText">Hide</span>
                                        <div class="bg-gray-200 group-hover:bg-gray-300 p-2 rounded-full transition-colors duration-300">
                                            <i id="toggleIcon" class="fas fa-chevron-up group-hover:rotate-180 transition-transform duration-300"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div id="completedTasksContainer" class="transition-all duration-500">
                            @foreach($completedTodos as $index => $todo)
                                <div class="group p-8 bg-gradient-to-r from-gray-50/30 to-green-50/20 hover:from-gray-50/50 hover:to-green-50/30 transition-all duration-300 animate-slide-up" style="animation-delay: {{ ($incompleteTodos->count() + $index) * 0.1 }}s;">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-6 flex-1">
                                            <!-- Status Toggle -->
                                            <div class="mt-2">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center shadow-lg">
                                                    <i class="fas fa-check text-white text-sm"></i>
                                                </div>
                                            </div>

                                            <!-- Todo Content -->
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-3 mb-2">
                                                            <h3 class="text-xl font-bold text-gray-500 line-through">
                                                                {{ $todo->title }}
                                                            </h3>
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg">
                                                                <i class="fas fa-lock mr-1"></i>Locked
                                                            </span>
                                                        </div>
                                                        
                                                        @if($todo->description)
                                                            <p class="text-gray-500 mb-4 line-through leading-relaxed">
                                                                {{ $todo->description }}
                                                            </p>
                                                        @endif

                                                        <div class="flex items-center space-x-4">
                                                            <!-- Status Badge -->
                                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg">
                                                                <i class="fas fa-check mr-2"></i>Completed
                                                            </span>

                                                            <!-- Deadline -->
                                                            @if($todo->deadline)
                                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                                    {{ \Carbon\Carbon::parse($todo->deadline)->format('M d, Y') }}
                                                                </span>
                                                            @endif

                                                            <!-- Completed Date -->
                                                            @if($todo->completed_at)
                                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700 border border-green-200">
                                                                    <i class="fas fa-check mr-2"></i>
                                                                    Completed {{ \Carbon\Carbon::parse($todo->completed_at)->format('M d, Y') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-3 ml-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                                            <!-- View Only Button (Disabled Edit) -->
                                            <button class="relative p-3 rounded-xl bg-gray-300 text-gray-500 cursor-not-allowed opacity-50" 
                                                    disabled
                                                    title="Cannot edit completed task">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Reopen Task Button -->
                                            <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="group/reopen relative p-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                                        title="Reopen task to edit">
                                                    <i class="fas fa-undo group-hover/reopen:rotate-180 transition-transform duration-300"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete Button (Still Available) -->
                                            <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this completed task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="group/delete relative p-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all duration-300 hover:scale-110 hover:shadow-lg">
                                                    <i class="fas fa-trash group-hover/delete:rotate-12 transition-transform duration-300"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Completed Task Notice -->
                                    <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-r-xl">
                                        <div class="flex items-center text-sm text-green-800">
                                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                                <i class="fas fa-info-circle text-green-600"></i>
                                            </div>
                                            <span class="font-semibold">This task is completed and cannot be edited. Click the <i class="fas fa-undo mx-1"></i> button to reopen if you need to make changes.</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-20">
                    <div class="relative mb-8">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-32 h-32 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-full blur-xl"></div>
                        </div>
                        <div class="relative">
                            <i class="fas fa-tasks text-gray-300 text-8xl mb-6 animate-bounce-in"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No tasks yet</h3>
                    <p class="text-gray-600 mb-8 text-lg">Get started by creating your first task and boost your productivity.</p>
                    <a href="{{ route('todos.create') }}" class="group inline-flex items-center space-x-3 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-accent text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                        <div class="bg-white/20 p-2 rounded-full group-hover:rotate-180 transition-transform duration-500">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>Add Your First Task</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Legend/Info Card -->
        <div class="mt-10 bg-white/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20 p-8 animate-fade-in">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-2xl mr-4">
                    <i class="fas fa-info-circle text-white text-xl"></i>
                </div>
                Task Management Guide
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-900 text-lg mb-4">Task Status</h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-yellow-50 rounded-2xl border border-yellow-200">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg mr-4">
                                <i class="fas fa-clock mr-2"></i>Pending
                            </span>
                            <span class="text-gray-700 font-medium">Active tasks that can be edited</span>
                        </div>
                        <div class="flex items-center p-4 bg-red-50 rounded-2xl border border-red-200">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg mr-4">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Late
                            </span>
                            <span class="text-gray-700 font-medium">Overdue tasks that can still be edited</span>
                        </div>
                        <div class="flex items-center p-4 bg-green-50 rounded-2xl border border-green-200">
                                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg mr-4">
                                <i class="fas fa-check mr-2"></i>Completed
                            </span>
                            <span class="text-gray-700 font-medium">Finished tasks (edit locked, moved to bottom)</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-900 text-lg mb-4">Available Actions</h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-50 rounded-2xl border border-blue-200">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-xl mr-4">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Edit task (only for pending/late tasks)</span>
                        </div>
                        <div class="flex items-center p-4 bg-indigo-50 rounded-2xl border border-indigo-200">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-xl mr-4">
                                <i class="fas fa-undo text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Reopen completed task to enable editing</span>
                        </div>
                        <div class="flex items-center p-4 bg-red-50 rounded-2xl border border-red-200">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 p-2 rounded-xl mr-4">
                                <i class="fas fa-trash text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Delete task (available for all statuses)</span>
                        </div>
                        <div class="flex items-center p-4 bg-green-50 rounded-2xl border border-green-200">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-2 rounded-xl mr-4">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Mark as complete/incomplete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        @if($todos->count() > 0)
            <div class="mt-10 relative overflow-hidden bg-gradient-to-r from-primary via-secondary to-accent rounded-3xl p-8 text-white shadow-2xl animate-fade-in">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
                
                <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-6 lg:space-y-0">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-4 flex items-center">
                            <div class="bg-white/20 p-3 rounded-2xl mr-4">
                                <i class="fas fa-chart-line text-2xl"></i>
                            </div>
                            Your Progress
                        </h3>
                        <p class="text-lg text-white/90 font-medium leading-relaxed">
                            You have completed <span class="font-bold text-2xl">{{ $stats['completed'] }}</span> out of <span class="font-bold text-2xl">{{ $stats['total'] }}</span> tasks
                            @if($stats['total'] > 0)
                                <span class="block mt-2 text-xl font-bold">
                                    ({{ round(($stats['completed'] / $stats['total']) * 100) }}% completion rate)
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex flex-col space-y-4">
                        @if($stats['late'] > 0)
                            <div class="bg-red-500/30 backdrop-blur-sm px-6 py-4 rounded-2xl border border-red-400/30">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-red-500 p-2 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                    <span class="font-bold text-lg">{{ $stats['late'] }} overdue task{{ $stats['late'] > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        @endif
                        @if($stats['pending'] > 0)
                            <div class="bg-yellow-500/30 backdrop-blur-sm px-6 py-4 rounded-2xl border border-yellow-400/30">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-yellow-500 p-2 rounded-full">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <span class="font-bold text-lg">{{ $stats['pending'] }} pending task{{ $stats['pending'] > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Enhanced Progress Bar -->
                @if($stats['total'] > 0)
                    <div class="relative mt-8">
                        <div class="flex justify-between text-sm text-white/80 mb-3">
                            <span class="font-semibold">Overall Progress</span>
                            <span class="font-bold">{{ round(($stats['completed'] / $stats['total']) * 100) }}%</span>
                        </div>
                        <div class="relative w-full bg-white/20 rounded-full h-4 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 rounded-full"></div>
                            <div class="relative bg-gradient-to-r from-white via-white/90 to-white h-4 rounded-full transition-all duration-1000 ease-out shadow-lg" 
                                 style="width: {{ ($stats['completed'] / $stats['total']) * 100 }}%">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-white/60 mt-2">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                            <span>75%</span>
                            <span>100%</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Enhanced JavaScript -->
    <script>
        // Enhanced auto-hide success messages with animation
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.querySelector('.bg-gradient-to-r.from-green-400');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transform = 'translateY(-100%) scale(0.95)';
                    successAlert.style.opacity = '0';
                    setTimeout(() => {
                        successAlert.remove();
                    }, 500);
                }, 5000);
            }

            // Add staggered animation to task items
            const taskItems = document.querySelectorAll('.group.p-8');
            taskItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.classList.add('animate-slide-up');
            });

            // Add hover effects to stats cards
            const statsCards = document.querySelectorAll('.group.relative.bg-white\\/70');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });

        // Enhanced toggle completed tasks with smooth animation
        function toggleCompletedTasks() {
            const container = document.getElementById('completedTasksContainer');
            const toggleBtn = document.getElementById('toggleCompletedBtn');
            const toggleText = document.getElementById('toggleText');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (container.style.maxHeight === '0px' || container.style.display === 'none') {
                // Show completed tasks
                container.style.display = 'block';
                container.style.maxHeight = container.scrollHeight + 'px';
                container.style.opacity = '1';
                toggleText.textContent = 'Hide';
                toggleIcon.className = 'fas fa-chevron-up group-hover:rotate-180 transition-transform duration-300';
                localStorage.setItem('completedTasksVisible', 'true');
            } else {
                // Hide completed tasks
                container.style.maxHeight = '0px';
                container.style.opacity = '0';
                toggleText.textContent = 'Show';
                toggleIcon.className = 'fas fa-chevron-down group-hover:rotate-180 transition-transform duration-300';
                localStorage.setItem('completedTasksVisible', 'false');
                
                setTimeout(() => {
                    container.style.display = 'none';
                }, 500);
            }
        }

        // Restore completed tasks visibility state with animation
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('completedTasksContainer');
            const toggleText = document.getElementById('toggleText');
            const toggleIcon = document.getElementById('toggleIcon');
            const isVisible = localStorage.getItem('completedTasksVisible');
            
            if (isVisible === 'false' && container) {
                container.style.maxHeight = '0px';
                container.style.opacity = '0';
                container.style.display = 'none';
                toggleText.textContent = 'Show';
                toggleIcon.className = 'fas fa-chevron-down group-hover:rotate-180 transition-transform duration-300';
            }
        });

        // Enhanced form submission with loading states and animations
        document.querySelectorAll('form[action*="toggle"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                const taskElement = this.closest('.group.p-8');
                
                // Add loading animation
                button.style.transform = 'scale(0.9)';
                button.style.opacity = '0.7';
                
                // Add completion animation for pending tasks
                const isCompleting = button.querySelector('.fas.fa-check');
                if (!isCompleting) {
                    taskElement.style.transform = 'scale(0.98)';
                    taskElement.style.opacity = '0.8';
                    
                    // Show success notification
                    setTimeout(() => {
                        showNotification('Task completed successfully! 🎉', 'success');
                    }, 300);
                }
            });
        });

        // Enhanced notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgGradient = type === 'success' ? 'from-green-500 to-emerald-500' : 
                              type === 'error' ? 'from-red-500 to-red-600' : 'from-blue-500 to-blue-600';
            
            notification.className = `fixed top-6 right-6 bg-gradient-to-r ${bgGradient} text-white px-6 py-4 rounded-2xl shadow-2xl z-50 flex items-center transform translate-x-full transition-all duration-500 max-w-md`;
            notification.innerHTML = `
                <div class="bg-white/20 p-2 rounded-full mr-4">
                    <i class="fas ${type === 'success' ? 'fa-check' : type === 'error' ? 'fa-times' : 'fa-info'} text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}!</p>
                    <p class="text-sm opacity-90">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/20 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            document.body.appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 500);
            }, 5000);
        }

        // Enhanced delete confirmation with better UX
        document.querySelectorAll('form[action*="destroy"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const taskElement = this.closest('.group.p-8');
                const taskTitle = taskElement.querySelector('h3').textContent.trim().replace('Locked', '').trim();
                const isCompleted = taskElement.classList.contains('completed-task') || taskElement.querySelector('.line-through');
                
                const confirmMessage = isCompleted 
                    ? `Are you sure you want to permanently delete the completed task "${taskTitle}"? This action cannot be undone.`
                    : `Are you sure you want to delete "${taskTitle}"? This action cannot be undone.`;
                
                // Create custom confirmation modal
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl transform scale-95 transition-transform duration-300">
                        <div class="text-center">
                                                       <div class="bg-red-100 p-4 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                                <i class="fas fa-trash text-red-500 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Delete Task</h3>
                            <p class="text-gray-600 mb-8">${confirmMessage}</p>
                            <div class="flex space-x-4">
                                <button onclick="this.closest('.fixed').remove()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-xl transition-colors duration-300">
                                    Cancel
                                </button>
                                <button onclick="confirmDelete(this)" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Animate modal in
                setTimeout(() => {
                    modal.querySelector('.bg-white').style.transform = 'scale(1)';
                }, 10);
                
                // Store form reference
                modal.formRef = this;
            });
        });

        function confirmDelete(button) {
            const modal = button.closest('.fixed');
            const form = modal.formRef;
            const taskElement = form.closest('.group.p-8');
            
            // Add deletion animation
            taskElement.style.transition = 'all 0.5s ease-in-out';
            taskElement.style.transform = 'translateX(-100%) scale(0.8)';
            taskElement.style.opacity = '0';
            
            modal.remove();
            
            setTimeout(() => {
                form.submit();
            }, 500);
        }

        // Keyboard shortcuts with visual feedback
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N for new task
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                showNotification('Redirecting to create new task...', 'info');
                setTimeout(() => {
                    window.location.href = "{{ route('todos.create') }}";
                }, 500);
            }
            
            // Ctrl/Cmd + H to toggle completed tasks
            if ((e.ctrlKey || e.metaKey) && e.key === 'h') {
                e.preventDefault();
                const toggleBtn = document.getElementById('toggleCompletedBtn');
                if (toggleBtn) {
                    toggleCompletedTasks();
                    showNotification('Toggled completed tasks visibility', 'info');
                }
            }
        });

        // Enhanced keyboard shortcut hints with better styling
        const keyboardHints = document.createElement('div');
        keyboardHints.className = 'fixed bottom-6 left-6 bg-gray-900/90 backdrop-blur-sm text-white text-sm px-6 py-4 rounded-2xl opacity-0 transition-all duration-300 shadow-2xl border border-gray-700';
        keyboardHints.innerHTML = `
            <div class="font-bold mb-3 text-center">⌨️ Keyboard Shortcuts</div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">New Task:</span>
                    <kbd class="bg-gray-700 px-2 py-1 rounded text-xs font-mono">Ctrl+N</kbd>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Toggle Completed:</span>
                    <kbd class="bg-gray-700 px-2 py-1 rounded text-xs font-mono">Ctrl+H</kbd>
                </div>
            </div>
        `;
        document.body.appendChild(keyboardHints);

        // Show keyboard hints on Alt key press
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                keyboardHints.style.opacity = '1';
                keyboardHints.style.transform = 'translateY(0)';
            }
        });

        document.addEventListener('keyup', function(e) {
            if (!e.altKey) {
                keyboardHints.style.opacity = '0';
                keyboardHints.style.transform = 'translateY(10px)';
            }
        });

        // Progress bar animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.bg-gradient-to-r.from-white.via-white\\/90');
            if (progressBar) {
                const targetWidth = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = targetWidth;
                }, 1000);
            }
        });

        // Smooth scroll behavior for better UX
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate-fade-in, .animate-slide-up');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease-out';
                observer.observe(el);
            });
        });

        // Enhanced task completion animation
        document.querySelectorAll('form[action*="toggle"] button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const taskElement = this.closest('.group.p-8');
                const isCompleting = !this.querySelector('.fas.fa-check');
                
                if (isCompleting) {
                    // Create completion effect
                    const completionEffect = document.createElement('div');
                    completionEffect.className = 'fixed inset-0 pointer-events-none z-40';
                    completionEffect.innerHTML = `
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="bg-green-500 text-white p-6 rounded-full shadow-2xl animate-bounce-in">
                                <i class="fas fa-check text-4xl"></i>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(completionEffect);
                    
                    setTimeout(() => {
                        completionEffect.remove();
                    }, 1500);
                }
            });
        });
    </script>

    <!-- Enhanced Custom Styles -->
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Glass morphism effects */
        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }

        .backdrop-blur-lg {
            backdrop-filter: blur(16px);
        }

        /* Enhanced hover effects */
        .group:hover .group-hover\:rotate-12 {
            transform: rotate(12deg);
        }

        .group:hover .group-hover\:rotate-180 {
            transform: rotate(180deg);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(243, 244, 246, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #667eea, #764ba2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #764ba2, #f093fb);
        }

        /* Smooth transitions for all elements */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Enhanced focus states */
        button:focus,
        a:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }

        /* Loading states */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced gradient text */
        .bg-clip-text {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Improved shadow effects */
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Custom border gradients */
        .border-gradient {
            border-image: linear-gradient(45deg, #667eea, #764ba2) 1;
        }

        /* Enhanced backdrop effects */
        .bg-white\/70 {
            background-color: rgba(255, 255, 255, 0.7);
        }

        .bg-white\/80 {
            background-color: rgba(255, 255, 255, 0.8);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .animate-fade-in {
                animation-delay: 0s !important;
            }
            
            .animate-slide-up {
                animation-delay: 0s !important;
            }
        }

        /* Print styles */
        @media print {
            .fixed,
            .sticky {
                position: static !important;
            }
            
            .bg-gradient-to-r,
            .bg-gradient-to-br {
                background: #f9fafb !important;
                color: #111827 !important;
            }
        }
    </style>
</body>
</html>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <i class="fas fa-edit text-primary text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Edit Task</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Edit Task</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Current Task Info with Priority -->
        @php
            $currentPriorityBadge = '';
            $currentBorderClass = '';
            
            if($todo->deadline && $todo->status !== 'completed') {
                $deadline = \Carbon\Carbon::parse($todo->deadline);
                $now = \Carbon\Carbon::now();
                $diffInDays = $now->diffInDays($deadline, false);
                
                if($diffInDays < 0) {
                    $currentPriorityBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 animate-pulse"><i class="fas fa-fire mr-1"></i>URGENT</span>';
                    $currentBorderClass = 'border-l-4 border-red-500';
                } elseif($diffInDays == 0) {
                    $currentPriorityBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800"><i class="fas fa-bell mr-1"></i>TODAY</span>';
                    $currentBorderClass = 'border-l-4 border-orange-500';
                } elseif($diffInDays == 1) {
                    $currentPriorityBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-arrow-right mr-1"></i>TOMORROW</span>';
                    $currentBorderClass = 'border-l-4 border-yellow-500';
                } elseif($diffInDays <= 7) {
                    $currentPriorityBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"><i class="fas fa-calendar-week mr-1"></i>THIS WEEK</span>';
                    $currentBorderClass = 'border-l-4 border-blue-500';
                }
            }
        @endphp

        <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 mb-8 text-white {{ $currentBorderClass }}">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold mb-1">Currently Editing:</h3>
                    <p class="text-indigo-100 text-lg">{{ $todo->title }}</p>
                    @if($todo->deadline)
                        <p class="text-indigo-200 text-sm mt-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Deadline: {{ \Carbon\Carbon::parse($todo->deadline)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    @if($todo->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Completed
                        </span>
                    @elseif($todo->status === 'late')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Late
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                    @endif
                    
                    @if($currentPriorityBadge)
                        <div class="mt-2">
                            {!! $currentPriorityBadge !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Update Task Details</h2>
                <p class="text-gray-600">Modify the task information below. Changes to deadline will affect task priority.</p>
            </div>

            <form action="{{ route('todos.update', $todo) }}" method="POST" class="space-y-6" id="updateForm">
                @csrf
                @method('PUT')
                
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-heading mr-2 text-primary"></i>Task Title *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $todo->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 @error('title') border-red-500 @enderror"
                           placeholder="Enter task title..."
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2 text-primary"></i>Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 @error('description') border-red-500 @enderror"
                              placeholder="Enter task description (optional)...">{{ old('description', $todo->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-primary"></i>Deadline
                    </label>
                    <input type="date" 
                                                      id="deadline" 
                           name="deadline" 
                           value="{{ old('deadline', $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 @error('deadline') border-red-500 @enderror">
                    @error('deadline')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Leave empty if no specific deadline. Tasks with deadlines will be prioritized.
                    </p>
                </div>

                <!-- Deadline Priority Preview -->
                <div id="deadlinePreview" class="hidden bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-eye mr-2 text-primary"></i>New Priority Preview
                    </h4>
                    <div id="priorityIndicator" class="text-sm"></div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2 text-primary"></i>Status *
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 @error('status') border-red-500 @enderror"
                            required>
                        <option value="pending" {{ old('status', $todo->status) === 'pending' ? 'selected' : '' }}>
                            📋 Pending
                        </option>
                        <option value="late" {{ old('status', $todo->status) === 'late' ? 'selected' : '' }}>
                            ⚠️ Late
                        </option>
                        <option value="completed" {{ old('status', $todo->status) === 'completed' ? 'selected' : '' }}>
                            ✅ Completed
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Task Metadata -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <h4 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>Task Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Created:</span>
                            <span class="text-gray-900 ml-2">{{ $todo->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="text-gray-900 ml-2">{{ $todo->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($todo->completed_at)
                            <div class="md:col-span-2">
                                <span class="text-gray-600">Completed:</span>
                                <span class="text-green-600 ml-2 font-medium">{{ \Carbon\Carbon::parse($todo->completed_at)->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route('home') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i>Update Task
                    </button>
                </div>
            </form>

            <!-- Form Delete Terpisah -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i>Delete Task
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Toggle Status -->
                <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="w-full">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                        @if($todo->status === 'completed')
                            <i class="fas fa-undo mr-2"></i>Mark as Pending
                        @else
                            <i class="fas fa-check mr-2"></i>Mark as Complete
                        @endif
                    </button>
                </form>

                <!-- Duplicate Task -->
                <a href="{{ route('todos.create') }}?duplicate={{ $todo->id }}" class="w-full bg-green-50 hover:bg-green-100 text-green-700 px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                    <i class="fas fa-copy mr-2"></i>Duplicate Task
                </a>

                <!-- View All Tasks -->
                <a href="{{ route('home') }}" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i>View All Tasks
                </a>
            </div>
        </div>

        <!-- Priority & Status Guide -->
        <div class="mt-8 bg-amber-50 border border-amber-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-amber-900 mb-3 flex items-center">
                <i class="fas fa-question-circle mr-2"></i>Priority & Status Guide
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-amber-900 mb-3">Deadline Priority</h4>
                    <div class="space-y-2 text-amber-800 text-sm">
                        <div class="flex items-center p-2 border-l-4 border-red-500 bg-red-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3 animate-pulse">
                                <i class="fas fa-fire mr-1"></i>URGENT
                            </span>
                            <span>Overdue tasks (highest priority)</span>
                        </div>
                        <div class="flex items-center p-2 border-l-4 border-orange-500 bg-orange-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-3">
                                <i class="fas fa-bell mr-1"></i>TODAY
                            </span>
                            <span>Due today</span>
                        </div>
                        <div class="flex items-center p-2 border-l-4 border-yellow-500 bg-yellow-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3">
                                <i class="fas fa-arrow-right mr-1"></i>TOMORROW
                            </span>
                            <span>Due tomorrow</span>
                        </div>
                        <div class="flex items-center p-2 border-l-4 border-blue-500 bg-blue-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                                <i class="fas fa-calendar-week mr-1"></i>THIS WEEK
                            </span>
                            <span>Due within 7 days</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-amber-900 mb-3">Task Status</h4>
                    <div class="space-y-3 text-amber-800 text-sm">
                        <div class="flex items-start">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3 mt-0.5">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            <span>Task is active and waiting to be completed</span>
                        </div>
                        <div class="flex items-start">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3 mt-0.5">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Late
                            </span>
                            <span>Task has passed its deadline and is overdue</span>
                        </div>
                        <div class="flex items-start">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3 mt-0.5">
                                <i class="fas fa-check mr-1"></i>Completed
                            </span>
                            <span>Task has been finished successfully</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for enhanced UX -->
    <script>
        // Deadline priority preview
        document.getElementById('deadline').addEventListener('change', function() {
            const deadlineValue = this.value;
            const previewDiv = document.getElementById('deadlinePreview');
            const priorityIndicator = document.getElementById('priorityIndicator');
            
            if (deadlineValue) {
                const deadline = new Date(deadlineValue);
                const today = new Date();
                const diffTime = deadline - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                let priorityHTML = '';
                
                if (diffDays < 0) {
                    priorityHTML = `
                        <div class="flex items-center p-2 border-l-4 border-red-500 bg-red-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3 animate-pulse">
                                <i class="fas fa-fire mr-1"></i>URGENT
                            </span>
                            <span class="text-gray-700">This task will be overdue (highest priority)</span>
                        </div>
                    `;
                } else if (diffDays === 0) {
                    priorityHTML = `
                        <div class="flex items-center p-2 border-l-4 border-orange-500 bg-orange-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-3">
                                <i class="fas fa-bell mr-1"></i>TODAY
                            </span>
                            <span class="text-gray-700">This task will be due today (high priority)</span>
                        </div>
                    `;
                } else if (diffDays === 1) {
                    priorityHTML = `
                        <div class="flex items-center p-2 border-l-4 border-yellow-500 bg-yellow-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3">
                                <i class="fas fa-arrow-right mr-1"></i>TOMORROW
                            </span>
                            <span class="text-gray-700">This task will be due tomorrow</span>
                        </div>
                    `;
                } else if (diffDays <= 7) {
                    priorityHTML = `
                        <div class="flex items-center p-2 border-l-4 border-blue-500 bg-blue-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                                <i class="fas fa-calendar-week mr-1"></i>THIS WEEK
                            </span>
                            <span class="text-gray-700">This task will be due in ${diffDays} day${diffDays > 1 ? 's' : ''}</span>
                        </div>
                    `;
                                    } else {
                    priorityHTML = `
                        <div class="flex items-center p-2 border-l-4 border-gray-500 bg-gray-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-3">
                                <i class="fas fa-calendar mr-1"></i>LATER
                            </span>
                            <span class="text-gray-700">This task will be due in ${diffDays} days</span>
                        </div>
                    `;
                }
                
                priorityIndicator.innerHTML = priorityHTML;
                previewDiv.classList.remove('hidden');
            } else {
                previewDiv.classList.add('hidden');
            }
        });

        // Auto-update status based on deadline
        document.getElementById('deadline').addEventListener('change', function() {
            const deadline = new Date(this.value);
            const today = new Date();
            const statusSelect = document.getElementById('status');
            
            if (deadline < today && statusSelect.value === 'pending') {
                statusSelect.value = 'late';
                showNotification('Status automatically changed to "Late" due to past deadline', 'warning');
            }
        });

        // Status change confirmation for completed tasks
        document.getElementById('status').addEventListener('change', function() {
            if (this.value === 'completed') {
                const confirmed = confirm('Mark this task as completed? This will set the completion timestamp and lock the task from editing.');
                if (!confirmed) {
                    this.value = '{{ old('status', $todo->status) }}';
                }
            }
        });

        // Simple notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-all duration-300 ${
                type === 'warning' ? 'bg-yellow-500' : 
                type === 'success' ? 'bg-green-500' : 
                'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Form validation enhancement
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            if (title.length < 3) {
                e.preventDefault();
                showNotification('Task title must be at least 3 characters long', 'warning');
                document.getElementById('title').focus();
                return false;
            }
        });

        // Character counter for title
        const titleInput = document.getElementById('title');
        const titleLabel = titleInput.previousElementSibling;
        
        titleInput.addEventListener('input', function() {
            const remaining = 255 - this.value.length;
            const counter = titleLabel.querySelector('.char-counter') || document.createElement('span');
            counter.className = 'char-counter text-xs text-gray-500 ml-2';
            counter.textContent = `(${remaining} characters remaining)`;
            
            if (!titleLabel.querySelector('.char-counter')) {
                titleLabel.appendChild(counter);
            }
            
            if (remaining < 20) {
                counter.className = 'char-counter text-xs text-red-500 ml-2';
            } else {
                counter.className = 'char-counter text-xs text-gray-500 ml-2';
            }
        });

        // Character counter for description
        const descInput = document.getElementById('description');
        const descLabel = descInput.previousElementSibling;
        
        descInput.addEventListener('input', function() {
            const remaining = 1000 - this.value.length;
            const counter = descLabel.querySelector('.char-counter') || document.createElement('span');
            counter.className = 'char-counter text-xs text-gray-500 ml-2';
            counter.textContent = `(${remaining} characters remaining)`;
            
            if (!descLabel.querySelector('.char-counter')) {
                descLabel.appendChild(counter);
            }
            
            if (remaining < 50) {
                counter.className = 'char-counter text-xs text-red-500 ml-2';
            } else {
                counter.className = 'char-counter text-xs text-gray-500 ml-2';
            }
        });

        // Initialize character counters
        titleInput.dispatchEvent(new Event('input'));
        descInput.dispatchEvent(new Event('input'));
    </script>

    <!-- Custom Styles -->
    <style>
        /* Pulse animation for urgent tasks */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</body>
</html>



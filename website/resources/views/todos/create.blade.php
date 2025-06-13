<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Task - Todo List</title>
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
                    <i class="fas fa-tasks text-primary text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">
                        @if(isset($duplicateTask))
                            Duplicate Task
                        @else
                            Add New Task
                        @endif
                    </h1>
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
                        <span class="text-sm font-medium text-gray-500">
                            @if(isset($duplicateTask))
                                Duplicate Task
                            @else
                                Add Task
                            @endif
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        @if(isset($duplicateTask))
            <!-- Duplicate Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                <div class="flex items-center">
                    <i class="fas fa-copy text-blue-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 mb-1">Duplicating Task</h3>
                        <p class="text-blue-700">Creating a copy of: <strong>{{ $duplicateTask->title }}</strong></p>
                        <p class="text-sm text-blue-600 mt-1">You can modify the details below before saving.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    @if(isset($duplicateTask))
                        Duplicate Task Details
                    @else
                        Create New Task
                    @endif
                </h2>
                <p class="text-gray-600">Fill in the details below to create a new task.</p>
            </div>

            <form action="{{ route('todos.store') }}" method="POST" class="space-y-6" id="createForm">
                @csrf
                
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-heading mr-2 text-primary"></i>Task Title *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', isset($duplicateTask) ? $duplicateTask->title : '') }}"
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
                              placeholder="Enter task description (optional)...">{{ old('description', isset($duplicateTask) ? $duplicateTask->description : '') }}</textarea>
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
                           value="{{ old('deadline', isset($duplicateTask) && $duplicateTask->deadline ? \Carbon\Carbon::parse($duplicateTask->deadline)->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d') }}"
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
                        <i class="fas fa-eye mr-2 text-primary"></i>Deadline Priority Preview
                    </h4>
                    <div id="priorityIndicator" class="text-sm"></div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('home') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        @if(isset($duplicateTask))
                            Create Duplicate
                        @else
                            Create Task
                        @endif
                    </button>
                </div>
            </form>
        </div>

        <!-- Tips Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-lightbulb mr-2"></i>Tips for Better Task Management
            </h3>
            <ul class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1 text-sm"></i>
                    <span>Use clear and specific titles for your tasks</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1 text-sm"></i>
                    <span>Add descriptions to provide context and details</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1 text-sm"></i>
                    <span>Set realistic deadlines to stay motivated</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1 text-sm"></i>
                    <span>Tasks with deadlines will appear first, sorted by urgency</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- JavaScript for Enhanced UX -->
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
                
                if (diffDays === 0) {
                    priorityHTML = `
                                                <div class="flex items-center p-2 border-l-4 border-orange-500 bg-orange-50">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-3">
                                <i class="fas fa-bell mr-1"></i>TODAY
                            </span>
                            <span class="text-gray-700">This task will be marked as due today (high priority)</span>
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

        // Form validation
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            if (title.length < 3) {
                e.preventDefault();
                alert('Task title must be at least 3 characters long');
                document.getElementById('title').focus();
                return false;
            }
        });

        // Auto-focus on title if duplicating
        @if(isset($duplicateTask))
            document.getElementById('title').focus();
            document.getElementById('title').select();
        @endif
    </script>
</body>
</html>

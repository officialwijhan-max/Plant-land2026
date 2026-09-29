<x-table :models="$tasks" filter_id="filter_id">
    <x-slot name="left_side_btn">
        <input type="text" name="sort" class="sort d-none" value="{{ request('sort', 'asc') }}">
        <input type="hidden" name="column" class="column d-none" value="{{ request('col') }}">
        @if(permissionCheck('add_contact.store'))
        @endif
    </x-slot>

    <x-slot name="table_btns">
        
      
        
        
    </x-slot>

    <x-slot name="table">
        <x-table.head>
            <tr>
                <!-- ID Column -->
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="?col=id&sort={{ request('sort') == 'asc' && request('col') == 'id' ? 'desc' : 'asc' }}">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.ID') }}
                    </a>
                </x-table.th>

               

                <!-- Project Column -->
                <x-table.th scope="col">Project</x-table.th>
                <!-- Task Column -->
                <x-table.th scope="col">Task</x-table.th>
                <!-- Assigned To Column -->
                <x-table.th scope="col">Assigned to</x-table.th>
                <!-- Due Date Column -->
                <x-table.th scope="col">Due date</x-table.th>
                <!-- Last Comment Column -->
                <x-table.th scope="col">Last comment</x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="completed" href="?col=completed&sort={{ request('sort') == 'asc' && request('col') == 'completed' ? 'desc' : 'asc' }}">
                        @if (request('sort') == 'asc' && request('col') == 'completed')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Status') }}
                    </a>
                </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @if ($tasks->isEmpty())
                <x-table.tr>
                    <x-table.td colspan="5" style="text-align: center;">
                        <em>No data available</em>
                    </x-table.td>
                </x-table.tr>
            @else
            @foreach ($tasks as $key => $task)
                <x-table.tr>
                    <!-- Row Number -->
                    <x-table.td>{{ $loop->iteration }}</x-table.td>
            
                    <!-- Project Name -->
                    <x-table.td>{{ $task['project_name'] ?? "---" }}</x-table.td>
            
                    <!-- Task Name -->
                    <x-table.td>{{ $task['task_name'] ?? "---" }}</x-table.td>
            
                    <!-- Assigned Users -->
                    <x-table.td>
                        @if (!empty($task['assigned_users']))
                            <ul>
                                @foreach ($task['assigned_users'] as $user)
                                    <li>{{ $user }}</li>
                                @endforeach
                            </ul>
                        @else
                            <em>No users</em>
                        @endif
                    </x-table.td>
            
                    <!-- Due Date -->
                    <x-table.td>
                        {{ $task['due_date'] ?? 'No due date' }}
                    </x-table.td>
            
                    <!-- Last Comment -->
                    <x-table.td>
                        {!! $task['last_comment'] ?? '---' !!}
                    </x-table.td>
                    <x-table.td>
                        @if ($task['completed'] == 0)
                            <span class="badge_3">{{__('account.Pending')}}</span>
                        @else
                        <span class="badge_1">Completed</span>
                        @endif
                    </x-table.td>
                </x-table.tr>
            @endforeach
        
            @endif
        </x-table.body>
       
        
    </x-slot>
</x-table>

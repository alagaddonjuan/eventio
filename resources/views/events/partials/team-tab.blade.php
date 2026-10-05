<div id="panel-team" class="hidden p-6">
    <h3 class="text-lg font-bold text-slate-800 mb-6">Event Team & Co-Hosts</h3>

    @if(session('team_success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6 font-semibold">
            {{ session('team_success') }}
        </div>
    @endif
    @if($errors->has('email'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6 font-semibold">
            {{ $errors->first('email') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <h4 class="text-md font-bold text-slate-800 mb-2">Invite Co-Host</h4>
        <p class="text-sm text-slate-500 mb-4">Invite other users to help manage this event. They must have an account.</p>
        
        <form action="{{ route('events.team.invite', $event) }}" method="POST" class="flex gap-4">
            @csrf
            <input type="email" name="email" placeholder="Co-host's email address" required class="flex-1 px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
            
            <select name="role" class="px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                <option value="co-host">Co-Host</option>
                <option value="manager">Manager</option>
            </select>
            
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-6 rounded-lg transition shadow-sm">
                Invite
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold">
                                {{ substr($event->user->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">{{ $event->user->name }} (You)</div>
                                <div class="text-xs text-slate-500">{{ $event->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Owner
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    </td>
                </tr>
                @foreach($event->cohosts as $cohost)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold">
                                {{ substr($cohost->user->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">{{ $cohost->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $cohost->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 capitalize">
                            {{ $cohost->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <form action="{{ route('events.team.remove', [$event, $cohost]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium" onclick="return confirm('Remove this team member?')">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($event->cohosts->isEmpty())
        <div class="p-6 text-center text-sm text-slate-500">
            No co-hosts added yet.
        </div>
        @endif
    </div>
</div>

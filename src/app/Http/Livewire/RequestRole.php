<?php

namespace App\Http\Livewire;

use App\Models\Role;
use App\Models\RoleRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RequestRole extends Component
{
    public function render()
    {
        $user = Auth::user();
        $roles = Role::orderBy("importance")->get();
        $request = RoleRequest::orderBy('created_at', "desc")->where("user", $user->id)->where("granted", null)->first();
        return view('livewire.request-role', compact('user', 'roles', 'request'));
    }
}

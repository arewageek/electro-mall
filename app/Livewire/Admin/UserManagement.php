<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $user_id = null;

    public $first_name = '';

    public $last_name = '';

    public $email = '';

    public $password = '';

    public $role = '';

    public $is_editing = false;

    public $show_modal = false;

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user_id],
            'password' => $this->is_editing ? ['nullable', Password::defaults()] : ['required', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ];
    }

    public function create()
    {
        $this->reset(['user_id', 'first_name', 'last_name', 'email', 'password', 'role']);
        $this->is_editing = false;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name ?? '';

        $this->is_editing = true;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        $user->syncRoles([$this->role]);

        $this->show_modal = false;

        $message = $this->user_id ? 'User updated successfully.' : 'User created successfully.';
        Flux::toast(variant: 'success', text: __($message));

        $this->reset(['user_id', 'first_name', 'last_name', 'email', 'password', 'role']);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            Flux::toast(variant: 'danger', text: __('You cannot delete your own account.'));

            return;
        }

        $user->delete();
        Flux::toast(variant: 'success', text: __('User deleted successfully.'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->with('roles')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => Role::all(),
        ])->layout('layouts.app');
    }
}

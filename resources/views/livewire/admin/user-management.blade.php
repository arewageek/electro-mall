<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">User Management</flux:heading>
            <flux:subheading>Manage employees, permissions, and roles.</flux:subheading>
        </div>
        
        <div class="flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search users..." />
            <flux:button wire:click="create" variant="primary" icon="plus">Add User</flux:button>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row>
                    <flux:table.cell class="font-medium">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" :name="$user->name" :initials="$user->initials()" />
                            {{ $user->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @foreach($user->roles as $user_role)
                            <flux:badge size="sm" color="zinc" inset="top bottom">{{ Str::title(str_replace('_', ' ', $user_role->name)) }}</flux:badge>
                        @endforeach
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                            <flux:menu>
                                <flux:menu.item wire:click="edit('{{ $user->id }}')" icon="pencil">Edit</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete('{{ $user->id }}')" wire:confirm="Are you sure you want to delete this user?" icon="trash" variant="danger">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center py-6 text-zinc-500">No users found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>
        {{ $users->links() }}
    </div>

    <flux:modal wire:model="showModal" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $isEditing ? 'Edit User' : 'Add New User' }}</flux:heading>
                <flux:subheading>Fill in the details for the warehouse employee.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" placeholder="John" />
                    <flux:error name="first_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" placeholder="Doe" />
                    <flux:error name="last_name" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Email Address</flux:label>
                <flux:input wire:model="email" type="email" placeholder="john@electromall.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Role</flux:label>
                <flux:select wire:model="role" placeholder="Choose a role...">
                    @foreach($roles as $db_role)
                        <flux:select.option value="{{ $db_role->name }}">{{ Str::title(str_replace('_', ' ', $db_role->name)) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="role" />
            </flux:field>

            <flux:field>
                <flux:label>Password {{ $isEditing ? '(Leave blank to keep current)' : '' }}</flux:label>
                <flux:input wire:model="password" type="password" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary">{{ $isEditing ? 'Save Changes' : 'Create User' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Supplier Management</flux:heading>
            <flux:subheading>Manage vendor records, contact persons, and supplier details.</flux:subheading>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search suppliers..." class="w-full sm:w-72" />
            <flux:button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">Add Supplier</flux:button>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Supplier</flux:table.column>
            <flux:table.column>Contact Person</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Phone</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($suppliers as $supplier)
                <flux:table.row>
                    <flux:table.cell class="font-medium">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" :name="$supplier->name" />
                            {{ $supplier->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $supplier->contact_person ?? 'N/A' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($supplier->email)
                            <a href="mailto:{{ $supplier->email }}" class="hover:underline">{{ $supplier->email }}</a>
                        @else
                            <span class="text-zinc-500">N/A</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $supplier->phone ?? 'N/A' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                            <flux:menu>
                                <flux:menu.item wire:click="edit('{{ $supplier->id }}')" icon="pencil">Edit</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete('{{ $supplier->id }}')" wire:confirm="Are you sure you want to delete this supplier?" icon="trash" variant="danger">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No suppliers found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>
        {{ $suppliers->links() }}
    </div>

    <flux:modal wire:model="show_modal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $is_editing ? 'Edit Supplier' : 'Add New Supplier' }}</flux:heading>
                <flux:subheading>Update the supplier and point of contact details.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Company Name</flux:label>
                <flux:input wire:model="name" placeholder="e.g. Acme Corporation" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Contact Person</flux:label>
                <flux:input wire:model="contact_person" placeholder="e.g. Jane Doe" />
                <flux:error name="contact_person" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="jane@acme.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="phone" placeholder="+1 (555) 123-4567" />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Address</flux:label>
                <flux:textarea wire:model="address" rows="3" placeholder="Full physical or mailing address..." />
                <flux:error name="address" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary">{{ $is_editing ? 'Save Changes' : 'Create Supplier' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

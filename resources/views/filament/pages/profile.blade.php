<x-filament-panels::page>

    <form wire:submit="update" id="form" class="grid gap-y-6">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()"  class="mt-4" />
    </form>
</x-filament-panels::page>
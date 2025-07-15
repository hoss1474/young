<x-filament::page>
    <x-filament::card>
        <x-filament::form wire:submit.prevent="submit">
            {{ $this->form }}
        </x-filament::form>

        <div class="mt-6">
            <x-filament::button type="submit" wire:click="submit" color="primary">
                ارسال پیام
            </x-filament::button>
        </div>
    </x-filament::card>
</x-filament::page>

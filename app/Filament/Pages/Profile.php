<?php

namespace App\Filament\Pages;

use App\Enums\AccountStatus;
use Filament\Pages\Page;
use App\Filament\Resources\LessonResource;
use App\Models\Profile as UserProfile;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Validation\Rules\In;
use Filament\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Profile extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'elemplus-setting';

    protected static string $view = 'filament.pages.profile';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public function mount()
    {
        $user = auth()->user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,

        ]);
    }

    /**
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Contact')->schema([
                    TextInput::make('email')
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->label('Email'),

                    TextInput::make('name')
                    ->required()
                        ->label('Name'),

                    TextInput::make('password')
                        ->password()
                        ->label('Password')
                        ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                        ->nullable(),



                    TextInput::make('password_confirmation')
                        ->password()
                        ->autocomplete('password')->same('password'),


                ])->columns(1),



            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('Update')
                ->color('primary')
                ->submit('Update'),
        ];
    }

    public function update()
    {
        $data = $this->form->getState();
        $user =   auth()->user();
        $user->update(
            $data
        );



        Notification::make()
            ->title('Profile updated!')
            ->success()
            ->send();
    }
}

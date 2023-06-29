<?php

namespace LaraZeus\Wind\Http\Livewire;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use LaraZeus\Wind\Events\LetterSent;
use LaraZeus\Wind\Models\Department;
use Livewire\Component;

/**
 * @property mixed $form
 */
class ContactsForm extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Department $department = null;

    public string $name = '';

    public string $email = '';

    public string $title = '';

    public string $message = '';

    public ?int $department_id;

    public bool $sent = false;

    public string $status = 'NEW';

    public function mount(string $departmentSlug = null): void
    {
        if (config('zeus-wind.enableDepartments')) {
            if ($departmentSlug !== null) {
                $this->department = config('zeus-wind.models.department')::where('slug', $departmentSlug)->first();
            } elseif (config('zeus-wind.defaultDepartmentId') !== null) {
                $this->department = config('zeus-wind.models.department')::find(config('zeus-wind.defaultDepartmentId'));
            }
        }

        $this->form->fill(
            [
                'department_id' => $this->department->id ?? null,
                'status' => config('zeus-wind.default_status'),
            ]
        );
    }

    public function store(): void
    {
        $letter = config('zeus-wind.models.letter')::create($this->form->getState());
        $this->sent = true;
        LetterSent::dispatch($letter);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()->schema([
                ViewField::make('department_id')
                    ->view(app('wind-theme') . '.departments')
                    ->columnSpan([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->label('')
                    ->visible(fn (): bool => config('zeus-wind.enableDepartments')),

                TextInput::make('name')
                    ->required()
                    ->minLength(6)
                    ->label(__('name')),

                TextInput::make('email')
                    ->required()
                    ->email()
                    ->label(__('email')),

            ])->columns([
                'default' => 1,
                'sm' => 1,
                'md' => 2,
            ]),

            Grid::make()->schema([
                TextInput::make('title')->required()->label(__('title')),
                Textarea::make('message')->required()->label(__('message')),
                Forms\Components\Hidden::make('status')->default('NEW'),
            ])->columns(1),
        ];
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view(app('wind-theme') . '.contact-form');
    }
}

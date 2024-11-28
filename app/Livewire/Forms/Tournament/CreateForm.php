<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Tournament;

use App\Rules\Tournaments\ValidTeamSize;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Livewire\Form;

class CreateForm extends Form
{
    public string $name = '';
    public ?string $description = null;
    public int $numberOfPlayers = 16;
    public bool $teamBased = false;
    public ?int $teamSize = 2;
    public bool $joinTournament = true;

    /** @return array<string, array<string|ValidationRule>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:256'],
            'description' => ['nullable', 'string', 'max:1000'],
            'numberOfPlayers' => ['required', 'integer', 'max:256', 'min:2'],
            'teamBased' => ['required', 'boolean'],
            'teamSize' => ['exclude_if:teamBased,false', 'required', 'integer', 'min:2', 'max:128', new ValidTeamSize($this->numberOfPlayers)],
            'joinTournament' => ['required', 'boolean'],
        ];
    }

    /**
     * @param array{description: string|null} $attributes
     *
     * @return array<string, mixed>
     */
    protected function prepareForValidation($attributes): array
    {
        $sanitizedDescription = Str::of((string) $attributes['description'])->deduplicate()->deduplicate("\n")->trim()->toString();
        $sanitizedDescription = 0 === strlen($sanitizedDescription) ? null : $sanitizedDescription;

        $this->description = $sanitizedDescription;
        $attributes['description'] = $sanitizedDescription;

        return $attributes;
    }
}

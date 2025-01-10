<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\PhaseType;
use App\Models\EliminationConfiguration;
use App\Models\Phase;
use App\Models\PhaseConfiguration;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Phase, PhaseConfiguration> */
class PhaseConfigurationCast implements CastsAttributes
{
    /**
     * Transform the JSON array into a PhaseConfiguration object.
     *
     * @param Phase $model
     * @param string $value
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, $value, array $attributes): PhaseConfiguration
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($value, true, flags: JSON_THROW_ON_ERROR);

        return match ($model->type) {
            PhaseType::ELIMINATION => EliminationConfiguration::fromArray($data),
            default => throw new \RuntimeException("Unknown phase type: {$model->type->value}"),
        };
    }

    /**
     * Transform the PhaseConfiguration object back into a JSON array for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, $value, array $attributes): string
    {
        if (!($value instanceof PhaseConfiguration)) {
            throw new \InvalidArgumentException('The given value must be an instance of PhaseConfiguration.');
        }

        return json_encode($value->toArray(), flags: JSON_THROW_ON_ERROR);
    }
}

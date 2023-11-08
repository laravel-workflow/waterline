<?php
declare(strict_types=1);

namespace Waterline\Dto;

use JsonSerializable;

class ChartDataPoint implements JsonSerializable
{
    public function __construct(
        private string $x,
        private float | int $yMin,
        private float | int $yMax,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'x' => $this->x,
            'y' => [$this->yMin, $this->yMax],
        ];
    }
}

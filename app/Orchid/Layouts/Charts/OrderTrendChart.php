<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class OrderTrendChart extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = self::TYPE_LINE;

    /**
     * Height of the chart.
     *
     * @var int
     */
    protected $height = 300;
}
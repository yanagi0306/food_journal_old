<?php

namespace App\Services\Order\Usen\Wrappers\Slip;

use App\Services\Base\CsvWrappers\ColumnBase;

/**
 * SalesType(販売形態)
 * example inputValue:「01:店内」
 * example value :「01」
 */
class SalesType extends ColumnBase
{
    protected string $permittedValueType     = 'string';
    protected ?bool  $isExtractLeft          = true;
}
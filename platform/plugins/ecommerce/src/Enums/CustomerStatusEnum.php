<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;
use Html;
use Illuminate\Support\HtmlString;

/**
 * @method static CustomerStatusEnum ACTIVATED()
 * @method static CustomerStatusEnum LOCKED()
 */
class CustomerStatusEnum extends Enum
{
    public const ACTIVATED = 'activated';
    public const LOCKED = 'locked';
    public const DELETED = 'Deleted';
    public static $langPath = 'plugins/ecommerce::customer.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ACTIVATED => Html::tag('span', self::ACTIVATED()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            self::LOCKED => Html::tag('span', self::LOCKED()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::DELETED => Html::tag('span', self::DELETED()->label(), ['class' => 'label-danger status-label'])
            ->toHtml(),
            default => parent::toHtml(),
        };
}
}

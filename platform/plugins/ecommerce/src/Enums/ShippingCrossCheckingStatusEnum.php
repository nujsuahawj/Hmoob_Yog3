<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static ShippingCrossCheckingStatusEnum PENDING()
 * @method static ShippingCrossCheckingStatusEnum COMPLETED()
 */
class ShippingCrossCheckingStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/ecommerce::shipping.cross_checking_statuses';
}

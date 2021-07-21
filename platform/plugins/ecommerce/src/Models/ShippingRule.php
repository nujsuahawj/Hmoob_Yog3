<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleItemInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingRule extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'ec_shipping_rules';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'currency_id',
        'type',
        'from',
        'to',
        'shipping_id',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @param string $value
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float)str_replace(',', '', $value);
    }

    /**
     * @param string $value
     */
    public function setFromAttribute($value)
    {
        $this->attributes['from'] = (float)str_replace(',', '', $value);
    }

    /**
     * @param string $value
     */
    public function setToAttribute($value)
    {
        if ($value == null) {
            $this->attributes['to'] = null;
        }
        $this->attributes['to'] = (float)str_replace(',', '', $value);
    }

    /**
     * @return string
     */
    public function getPriceAttribute()
    {
        return number_format($this->attributes['price'], 0, false, false);
    }

    /**
     * @return string
     */
    public function getFromAttribute()
    {
        return number_format($this->attributes['from'], 0, false, false);
    }

    /**
     * @return string
     */
    public function getToAttribute()
    {
        return number_format($this->attributes['to'], 0, false, false);
    }

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(ShippingRuleItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (ShippingRule $shippingRule) {
            app(ShippingRuleItemInterface::class)->deleteBy(['shipping_rule_id' => $shippingRule->id]);
        });
    }
}

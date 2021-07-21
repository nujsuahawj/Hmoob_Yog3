<?php

namespace Botble\Newsletter\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;

class NewsletterCacheDecorator extends CacheAbstractDecorator implements NewsletterInterface
{

}

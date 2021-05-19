<?php

namespace Modules\Application\Libs;

use Foundation\Services\TopUpService;
use Modules\Application\Http\DTOs\Product\HomeProductCollection;

/**
 * Class PatternWise
 * @package Modules\Application\Libs
 */
final class PatternWise
{

    private TopUpService $topUpService;

    private const PATTERN_HOT_OFFER = 'hot-offer';

    private const PATTERN_BEST_SELLER = 'best-seller';

    private const PATTERN_RECOMMENDED = 'recommended';

    private const PATTERN_TOP_WISHLIST = 'top-wishlist';

    public function __construct( TopUpService $topUpService)
    {
        $this->topUpService = $topUpService;
    }

    public function get($pattern): array
    {
        return HomeProductCollection::fromArray($this->topUpService->hotOffers(4)->all())->items();
    }

}

<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RecommendationTypes implements OptionSourceInterface
{
    public const RECOMMENDATION_TYPE_NOT_SET = 0;
    public const RECOMMENDATION_TYPE_CROSS_SELL = 1;
    public const RECOMMENDATION_TYPE_CUSTOM = 2;

    public function toOptionArray()
    {
        return [
            self::RECOMMENDATION_TYPE_NOT_SET => __('Category'),
            self::RECOMMENDATION_TYPE_CROSS_SELL => __('Cross sell'),
            self::RECOMMENDATION_TYPE_CUSTOM => __('Custom'),
        ];
    }
}

<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.36
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Model\Index\Magento\Catalog;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Profiler;
use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Review\Model\ReviewFactory;
use Mirasvit\SearchAutocomplete\Model\Config;
use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends AbstractIndex
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var ReviewRenderer
     */
    protected $reviewRenderer;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @param Config         $config
     * @param ReviewFactory  $reviewFactory
     * @param ReviewRenderer $reviewRenderer
     * @param ImageHelper    $imageHelper
     * @param CatalogHelper  $catalogHelper
     * @param PricingHelper  $pricingHelper
     */
    public function __construct(
        Config $config,
        ReviewFactory $reviewFactory,
        ReviewRenderer $reviewRenderer,
        ImageHelper $imageHelper,
        CatalogHelper $catalogHelper,
        PricingHelper $pricingHelper
    ) {
        $this->config = $config;
        $this->reviewFactory = $reviewFactory;
        $this->reviewRenderer = $reviewRenderer;
        $this->imageHelper = $imageHelper;
        $this->catalogHelper = $catalogHelper;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);

        $collection->addAttributeToSelect('short_description')
            ->addAttributeToSelect('description');

        if ($this->config->isShowRating()) {
            $this->reviewFactory->create()->appendSummary($collection);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->collection->getSize();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getItems()
    {
        Profiler::start(__METHOD__);

        $items = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->collection as $product) {
            $item = [
                'sku'         => $product->getSku(),
                'name'        => $product->getName(),
                'url'         => $product->getProductUrl(),
                'description' => null,
                'image'       => null,
                'price'       => null,
                'rating'      => null,
            ];

            if ($this->config->isShowShortDescription()) {
                $item['description'] = html_entity_decode(
                    strip_tags($product->getDataUsingMethod('description'))
                );
            }

            $image = false;
            if ($product->getImage() && $product->getImage() != 'no_selection') {
                $image = $product->getImage();
            } elseif ($product->getSmallImage() && $product->getSmallImage() != 'no_selection') {
                $image = $product->getSmallImage();
            }

            if ($this->config->isShowImage() && $image) {
                $item['image'] = $this->imageHelper->init($product, false)
                    ->setImageFile($image)
                    ->resize(65 * 2, 80 * 2)
                    ->getUrl();
            }

            if ($this->config->isShowPrice()) {
                $item['price'] = $this->catalogHelper->getTaxPrice($product, $product->getFinalPrice());
                $item['price'] = $this->pricingHelper->currency($item['price'], false, false);
            }

            if ($this->config->isShowRating()) {
                $item['rating'] = $this->reviewRenderer
                    ->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
            }

            $items[] = $item;
        }

        Profiler::stop(__METHOD__);

        return $items;
    }
}

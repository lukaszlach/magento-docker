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
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.34
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Model\Search;

use Mirasvit\Search\Model\Search\RequestGenerator;
use Magento\Framework\Config\ReaderInterface;

class ReaderPlugin
{
    /**
     * @var RequestGenerator
     */
    private $requestGenerator;

    /**
     * @param RequestGenerator $requestGenerator
     */
    public function __construct(
        RequestGenerator $requestGenerator
    ) {
        $this->requestGenerator = $requestGenerator;
    }

    /**
     * @param ReaderInterface $subject
     * @param \Closure        $proceed
     * @param null            $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRead(
        ReaderInterface $subject,
        \Closure $proceed,
        $scope = null
    ) {
        $result = $proceed($scope);

        $result = array_merge_recursive($result, $this->requestGenerator->generate());

        return $result;
    }
}

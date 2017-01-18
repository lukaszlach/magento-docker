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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit\Renderer;

use Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element;
use Mirasvit\Search\Model\Index;

/**
 * @method Index getIndex()
 */
class Attributes extends Element
{
    /**
     * @var string
     */
    private $template = 'Mirasvit_Search::index/edit/renderer/attributes.phtml';

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        $templateFile = $this->getTemplateFile($this->template);
        return $this->fetchView($templateFile);
    }

    /**
     * Index attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getIndex()->getIndexInstance()->getAttributes();
    }

    /**
     * Weights for attributes
     *
     * @return array
     */
    public function getAttributeWeights()
    {
        return $this->getIndex()->getIndexInstance()->getAttributeWeights();
    }
}

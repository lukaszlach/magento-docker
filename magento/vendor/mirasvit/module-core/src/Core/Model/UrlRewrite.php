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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @method string getUrlKey()
 * @method $this setUrlKey($key)
 *
 * @method string getType()
 * @method $this setType($type)
 *
 * @method string getModule()
 * @method $this setModule($module)
 *
 * @method int getEntityId()
 */
class UrlRewrite extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Core\Model\ResourceModel\UrlRewrite');
    }
}

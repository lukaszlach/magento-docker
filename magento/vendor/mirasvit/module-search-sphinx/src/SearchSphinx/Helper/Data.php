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


namespace Mirasvit\SearchSphinx\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param string $template
     * @param array  $variables
     *
     * @return string
     */
    public function filterTemplate($template, array $variables)
    {
        foreach ($variables as $var => $value) {
            $template = str_replace("{{var $var}}", $value, $template);
        }

        return $template;
    }

    /**
     * @param string $command
     * @return array
     * @throws \Exception
     */
    public function exec($command)
    {
        $status = null;
        $data = [];

        if (function_exists('exec')) {
            exec($command, $data, $status);
        } else {
            throw new \Exception(__('PHP function "exec" not available'));
        }

        return ['status' => $status, 'data' => implode(PHP_EOL, $data)];
    }
}

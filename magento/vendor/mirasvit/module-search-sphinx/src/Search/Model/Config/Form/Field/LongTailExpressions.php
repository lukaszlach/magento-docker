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


namespace Mirasvit\Search\Model\Config\Form\Field;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class LongTailExpressions extends ArraySerialized
{
    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $expressions = $this->getValue();

        foreach ($expressions as $rowKey => $row) {
            if ($rowKey === '__empty') {
                continue;
            }

            foreach (['match_expr', 'replace_expr'] as $fieldName) {
                if (!isset($row[$fieldName])) {
                    throw new \Exception(
                        __('Expression does not contain field \'%1\'', $fieldName)
                    );
                }
            }

            $expressions[$rowKey]['match_expr'] = $this->composeRegexp($row['match_expr']);
            $expressions[$rowKey]['replace_expr'] = $this->composeRegexp($row['replace_expr']);
        }

        $this->setValue($expressions);

        return parent::beforeSave();
    }

    /**
     * Prepare regular expression
     *
     * @param string $search
     * @return string
     * @throws \Exception
     */
    protected function composeRegexp($search)
    {
        // If valid regexp entered - do nothing
        if (@preg_match($search, '') !== false) {
            return $search;
        }

        // Find out - whether user wanted to enter regexp or normal string.
        if ($this->isRegexp($search)) {
            throw new \Exception(__('Invalid regular expression: "%1".', $search));
        }

        return '/' . preg_quote($search, '/') . '/i';
    }

    /**
     * Is regular expression?
     *
     * @param string $search
     * @return bool
     */
    protected function isRegexp($search)
    {
        if (strlen($search) < 3) {
            return false;
        }

        $possibleDelimiters = '/#~%';
        // Limit delimiters to reduce possibility, that we miss string with regexp.

        // Starts with a delimiter
        if (strpos($possibleDelimiters, $search[0]) !== false) {
            return true;
        }

        // Ends with a delimiter and (possible) modifiers
        $pattern = '/[' . preg_quote($possibleDelimiters, '/') . '][imsxeADSUXJu]*$/';
        if (preg_match($pattern, $search)) {
            return true;
        }

        return false;
    }
}

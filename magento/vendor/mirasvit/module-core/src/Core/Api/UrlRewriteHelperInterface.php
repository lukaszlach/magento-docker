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


namespace Mirasvit\Core\Api;

interface UrlRewriteHelperInterface
{
    /**
     * Enable/disable rewrites for module
     *
     * @param string $module    module alias (kbase)
     * @param bool   $isEnabled enable or disable
     * @return $this
     */
    public function setRewriteMode($module, $isEnabled);

    /**
     * Register base path for module
     *
     * @param string $module module alias (kbase)
     * @param string $path   base path (knowledge-base)
     * @return $this
     */
    public function registerBasePath($module, $path);

    /**
     * Register new path for module
     *
     * @param string $module   module alias (kbase)
     * @param string $type     path type (article)
     * @param string $template path template ([category_key]/[article_key])
     * @param string $action   controller action (kbase_article_view)
     * @param array  $params   additional params
     * @return $this
     */
    public function registerPath($module, $type, $template, $action, $params = []);

    /**
     * Return absolute url for entity
     *
     * @param string $module module alias (kbase)
     * @param string $type   path type (article)
     * @param object $entity entity
     * @return string absolute url for entity
     */
    public function getUrl($module, $type, $entity = null);

    /**
     * Normalize given string.
     * Example: ü -> ue.
     *
     * @param string $string
     * @return string
     */
    public function normalize($string);

    /**
     * Update url rewrite
     *
     * @param string $module
     * @param string $type
     * @param object $entity
     * @param array  $values
     * @return bool
     */
    public function updateUrlRewrite($module, $type, $entity, $values);

    /**
     * Delete url rewrite
     *
     * @param string $module
     * @param string $type
     * @param object $entity
     * @return bool
     */
    public function deleteUrlRewrite($module, $type, $entity);

    /**
     * Math path
     *
     * @param string $pathInfo
     * @return object
     */
    public function match($pathInfo);
}
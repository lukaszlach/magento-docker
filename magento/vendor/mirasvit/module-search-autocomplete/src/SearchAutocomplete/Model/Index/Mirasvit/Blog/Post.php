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


namespace Mirasvit\SearchAutocomplete\Model\Index\Mirasvit\Blog;

use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;

class Post extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->collection->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        /** @var \Mirasvit\Blog\Model\Post $post */
        foreach ($this->collection as $post) {
            $items[] = [
                'name' => $post->getName(),
                'url'  => $post->getUrl(),
            ];
        }

        return $items;
    }
}

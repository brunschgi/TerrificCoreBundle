<?php

/*
 * This file is part of the Terrific Core package.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\CoreBundle\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Filters assets through JsMin.
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class JsMinFilter implements FilterInterface
{
    private $filters;
    private $plugins;

    public function __construct()
    {
        $this->filters = array();
        $this->plugins = array();
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
    }

    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public function setPlugin($name, $value)
    {
        $this->plugins[$name] = $value;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $filters = $this->filters;
        $plugins = $this->plugins;

        if (isset($filters['ImportImports']) && true === $filters['ImportImports']) {
            $root = $asset->getSourceRoot();
            $path = $asset->getSourcePath();
            if ($root && $path) {
                $filters['ImportImports'] = array('BasePath' => dirname($root.'/'.$path));
            } else {
                unset($filters['ImportImports']);
            }
        }

        $asset->setContent(\JsMin::minify($asset->getContent(), $filters, $plugins));
    }
}

<?php

/*
 * This file is part of the Terrific Core Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\CoreBundle\Filter;

use Assetic\Filter\BaseCssFilter;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fixes CSS urls for Terrific Resources.
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class TerrificRewriteFilter extends BaseCssFilter
{
    private $container;

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $basePath = $this->container->get('request')->getBasePath();
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();
        $targetPath = $asset->getTargetPath();

        if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
            return;
        }

        $module = str_replace('../src/Terrific/Module/', '', $asset->getSourcePath());
        $parts = explode('/', $module);
        $module = 'terrificmodule'.strtolower($parts[0]);

        $content = $this->filterReferences($asset->getContent(), function($matches) use($basePath, $module)
        {
            if ('/' == $matches['url'][0]) {
                // root relative
                return str_replace($matches['url'], $basePath.$matches['url'], $matches[0]);
            }
            else if(strpos($matches['url'], '../') === 0) {
                // relative to module
                $image = str_replace('../', '', $matches['url']);
                return str_replace($matches['url'], $basePath.'/bundles/'.$module.'/'.$image, $matches[0]);
            }
            else {
                // do noting
                return $matches['url'];
            }
        });

        $asset->setContent($content);
    }

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

}

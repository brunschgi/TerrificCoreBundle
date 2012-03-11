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
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $sourcePath = $asset->getSourcePath();
        $targetPath = $asset->getTargetPath();

        if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
            return;
        }

        // pop entries off the target until it fits in the source
        if ('.' == dirname($sourcePath)) {
            $path = str_repeat('../', substr_count($targetPath, '/'));
        } elseif ('.' == $targetDir = dirname($targetPath)) {
            $path = dirname($sourcePath).'/';
        } else {
            $path = '';
            while (0 !== strpos($sourcePath, $targetDir)) {
                if (false !== $pos = strrpos($targetDir, '/')) {
                    $targetDir = substr($targetDir, 0, $pos);
                    $path .= '../';
                } else {
                    $targetDir = '';
                    $path .= '..';
                    break;
                }
            }
        }

        $module = str_replace('../src/Terrific/Module/', '', $asset->getSourcePath());
        $parts = explode('/', $module);
        $module = 'terrificmodule'.strtolower($parts[0]);

        $content = $this->filterReferences($asset->getContent(), function($matches) use($path, $module)
        {
            if ('/' == $matches['url'][0]) {
                // root relative
                return str_replace($matches['url'], $path.$matches['url'], $matches[0]);
            }
            else if(strpos($matches['url'], '../') === 0) {
                // relative to module
                $image = basename($matches['url']);
                return str_replace($matches['url'], $path.'/bundles/'.$module.'/img/'.$image, $matches[0]);
            }
            else {
                // do noting
                return $matches['url'];
            }
        });

        $asset->setContent($content);
    }

    private function getBasePath()
    {
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }
}

<?php

/*
 * This file is part of the Terrific Core Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Finder\Finder;

/**
 * ResourceListener deployes all Terrific module img resources automatically.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The deployment is only done in dev mode and if it is the master request.
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class ResourceListener
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var bool flag that indicates whether to copy the images on every request
     */
    private $copyImages;

    public function __construct(KernelInterface $kernel, $copyImages)
    {
        $this->kernel = $kernel;
        $this->copyImages = $copyImages;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // prevent copying completely if config is set
        if($this->copyImages) {

            if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            $request = $event->getRequest();

            // do not capture redirects or modify XML HTTP Requests
            if ($request->isXmlHttpRequest()) {
                return;
            }

            $baseUrl = $this->kernel->getRootDir();

            // update terrific resources
            $dir = $baseUrl.'/../src/Terrific/Module/';

            $finder = new Finder();
            $finder->directories()->in($dir)->depth('== 0');

            @mkdir($baseUrl.'/../web/bundles');

            foreach ($finder as $file) {
                // deploy module images
                $source = $file->getRealpath().'/Resources/public/img';

                if(file_exists($source)) {
                    $moduleDir = $baseUrl.'/../web/bundles/terrificmodule'.strtolower($file->getFilename());
                    @mkdir($moduleDir);

                    if(is_writeable($moduleDir)) {
                        $this->recursiveCopy($source, $baseUrl.'/../web/bundles/terrificmodule'.strtolower($file->getFilename()).'/img');
                    }
                    else {
                        throw new \RuntimeException('Unable to write module directory '.$moduleDir);
                    }
                }
            }

        }
    }

    private function recursiveCopy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);

        if(is_writeable($dst)) {
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' ) && ( strpos($file, '.') !== 0)) {
                    if ( is_dir($src . '/' . $file) ) {
                        $this->recursiveCopy($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        // only copy modified images
                        if(!file_exists($dst . '/' . $file) || filemtime($src . '/' . $file) > filemtime ($dst . '/' . $file))  {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
            }
            closedir($dir);
        }
        else {
            throw new \RuntimeException('Unable to write image directory '.$dst);
        }
    }
}

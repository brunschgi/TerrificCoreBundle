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
 * CoreListener deployes all Terrific resources (composition and modules) automatically.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The deployment is only done in dev mode and if it is the master request.
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class CoreListener
{
    private $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface       $kernel       The kernel is used to parse bundle notation
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $baseUrl = $this->kernel->getRootDir();

        if (in_array($this->kernel->getEnvironment(), array('dev'))) {

            // update terrific resources
            $dir = $baseUrl.'/../src/Terrific/Module/';

            $finder = new Finder();
            $finder->directories()->in($dir)->depth('== 0');

            // deploy composition resources
            @mkdir($baseUrl.'/../web/bundles');
            // $this->recursiveDelete($baseUrl.'/../web/bundles/terrificcomposition');
            $this->recursiveCopy($baseUrl.'/../src/Terrific/Composition/Resources/public', $baseUrl.'/../web/bundles/terrificcomposition');

            foreach ($finder as $file) {
                // deploy module resources
                // $this->recursiveDelete($baseUrl.'/../web/bundles/terrificmodule'.strtolower($file->getFilename()));
                $this->recursiveCopy($file->getRealpath().'/Resources/public', $baseUrl.'/../web/bundles/terrificmodule'.strtolower($file->getFilename()));
            }
        }
    }

    private function recursiveCopy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' ) && ( strpos($file, '.') !== 0)) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recursiveCopy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function recursiveDelete($target) {
        $dir = opendir($target);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($target . '/' . $file) ) {
                    $this->recursiveDelete($target . '/' . $file);
                }
                else {
                    unlink($target . '/' . $file);
                }
            }
        }
        rmdir($target);
    }
}

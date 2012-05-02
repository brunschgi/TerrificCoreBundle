<?php

/*
 * This file is part of the Terrific Core Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\CoreBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig_Test_Method;
use Symfony\Component\Finder\Finder;

class TerrificCoreExtension extends \Twig_Extension
{
    private $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel The kernel is used to get the root dir
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    function initRuntime(\Twig_Environment $environment)
    {
        // extend the loader paths
        $currentLoader = $environment->getLoader();
        $dir =  $this->kernel->getRootDir().'/../src/Terrific/Composition/Resources/macros/';
        $currentLoader->setPaths(array_merge($currentLoader->getPaths(), array(__DIR__, $dir)));

        // load the core macros
        $environment->addGlobal('tc', $environment->loadTemplate('terrificcore.html.twig'));

        // load the composition macros
        $finder = new Finder();
        $finder->files()->in($dir)->depth('== 0');

        foreach ($finder as $file) {
           $filename = $file->getFilename();

           if(strpos($filename, 'html') !== false) {
                $parts = explode('.', $file->getFilename());
                $macro = $parts[0];
                $environment->addGlobal($macro, $environment->loadTemplate($file->getFilename()));
           }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            'containing' => new Twig_Test_Method($this, 'twigTestContaining')
        );
    }

    function twigTestContaining($value, $needle)
    {
        return strpos($value, $needle) !== false;
    }


    /**
     * {@inheritdoc}
     */
    function getName()
    {
        return 'terrific_core';
    }
}




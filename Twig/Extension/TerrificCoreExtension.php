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
use Twig_Filter_Method;
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
        $currentLoader->setPaths(array_merge($currentLoader->getPaths(), array(__DIR__)));

        // load the core macros
        $environment->addGlobal('tc', $environment->loadTemplate('terrificcore.html.twig'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'dash' => new Twig_Filter_Method($this, 'dash'),
        );
    }

    public static function dash($value) {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1-\\2', '\\1-\\2'), $value));
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            'containing' => new Twig_Test_Method($this, 'containing')
        );
    }

    public static function containing($value, $needle)
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




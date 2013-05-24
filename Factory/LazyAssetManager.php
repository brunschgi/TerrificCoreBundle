<?php
/**
 * User: blorenz
 * Date: 05.11.12
 * Time: 08:05
 * To change this template use File | Settings | File Templates.
 */


namespace Terrific\CoreBundle\Factory {

    /**
     * Extends default LazyAssetManager to ignore default TerrificAssets.
     *
     */
    class LazyAssetManager extends \Assetic\Factory\LazyAssetManager
    {
        public $service;

        /**
         * @return array
         */
        public function getNames()
        {
            $ret = parent::getNames();


            if ($this->service->getContainer()->getParameter('terrific_core.resources.ignore_base_assets')) {
                $nRet = array();
                foreach ($ret as $asset) {
                    $formula = $this->getFormula($asset);

                    if (isset($formula[2])) {
                        $data = $formula[2];

                        if ($data["output"] != "css/compiled/base.css" && $data["output"] != "js/compiled/base.js") {
                            $nRet[] = $asset;
                        }
                    }
                }
                return $nRet;
            }

            return $ret;
        }
    }

}

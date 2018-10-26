<?php

namespace Wpp\SmartSlider3RestApi\Libraries;

use N2Loader;
use N2SmartSliderExport;
use N2AssetsManager;
use N2AssetsPredefined;
use N2Base;
use N2Platform;

N2Loader::import('libraries.export', 'smartslider');

class N2SmartSliderExportExtended extends N2SmartSliderExport {

    /**
     * Had to be replicated here, because the base class' $sliderId property is private.
     * @var int|string The unique identifier of a slider (numeric ID or alias).
     */
    private $sliderId;

    public function __construct($sliderId) {
        parent::__construct($sliderId);
        $this->sliderId = $sliderId;
    }

    /**
     * Get a slider's code split to parts:
     *  - a head part (containing CSS & JS);
     *  - a body part (the actual HTML + some other, slider-specific CSS & JS).
     * @return array An array containing 2 (key => value) pairs:
     *  - head;
     *  - body.
     */
    public function createHTMLParts() {
        n2_ob_end_clean_all();
        N2AssetsManager::createStack();

        N2AssetsPredefined::frontend(true);

        ob_start();
        N2Base::getApplication("smartslider")
              ->getApplicationType('frontend')
              ->render(array(
                  "controller" => 'home',
                  "action"     => N2Platform::getPlatform(),
                  "useRequest" => false
              ), array(
                  $this->sliderId,
                  'Export as HTML Parts'
              ));

        $sliderHTML   = ob_get_clean();

        $headHTML = N2AssetsManager::getCSS(false) . N2AssetsManager::getJs(false);

        return array(
            'head' => $headHTML,
            'body' => $sliderHTML,
        );
    }
}

/* EOF */

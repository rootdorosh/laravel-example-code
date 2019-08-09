<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract as Transformer;

/**
 * Class EventActivityTransformer.
 */
abstract class AbstractTransformer extends Transformer
{
    /**
     * default includes
     *
     * @var array
     */
    protected $itemIncludes = [];

    /**
     * params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Set item includes
     *
     * @param array $params
     * @param array $addtAttrs
     * @return self
     */
    public function setItemIncludes(array $params = [], $addtAttrs = []) : self
    {
        $this->params = $params;
        
        $attrs = $this->itemIncludes;
        foreach ($addtAttrs as $addtAttr) {
            $attrs[] = $addtAttr;
        }
     
        $this->setDefaultIncludes($attrs);

        return $this;
    }
}

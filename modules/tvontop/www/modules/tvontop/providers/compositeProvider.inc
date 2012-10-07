<?php
require_once 'providers/provider.inc';

abstract class CompositeProvider extends Provider {
    protected $preprocessors = array();
    
    function getObject($objectId,$params) {
        $objects = array();
        foreach ($this->preprocessors as $pp) {
            $objects[] = $pp->getObject($objectId,$params);
        }
        return mixObjects($objects);
    }

    function mixObjects($objects) {
        if(count($objects) > 0 ) {
            $mixedObject =  $objects[0];

            $tt = new MediaObject();

            for($i=1;$i<count($objects);$i++) {
                //              TODO: mix objects and children
            }
        }
    }
}
?>

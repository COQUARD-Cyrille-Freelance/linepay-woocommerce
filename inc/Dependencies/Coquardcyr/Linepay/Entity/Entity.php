<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity;

use JsonSerializable;

class Entity implements JsonSerializable
{
    /**
     * Serialize properties from the entity for later JSON encodage.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $attributes = get_class_vars(get_class($this));
        $rtn = array();
        foreach (array_keys($attributes) as $var) {
            if(is_array($this->{$var})) {
                $rtn[$var] = array_map(static function ($e) {
                    return self::parseJsonAttribute($e);
                }, $this->{$var});
            } else {
                $rtn[$var] = $this->parseJsonAttribute($this->{$var});
            }
        }
        return $rtn;
    }

    /**
     * Serialize an attribute for JSON.
     *
     * @param mixed $value value of the attribute to be serialized.
     * @return mixed
     */
    protected static function parseJsonAttribute($value) {
        if($value instanceof JsonSerializable) {
            return $value->jsonSerialize();
        }

        return $value;
    }
}

<?php

namespace Dododedodonl\LaravelResourceKit\Helpers;

class Action
{
    public $glyphicon, $text;

    protected $route, $parameters;

    public function __construct($route, $glyphicon = null, $text = null, $parameters = [])
    {
        $this->route = $route;
        $this->glyphicon = $glyphicon;
        $this->text = $text;
        $this->parameters = $parameters;
    }

    public function url($parameterValue = null)
    {
        $parameter = $this->parameters;
        if(isset($parameterValue)) {
            if(is_array($parameterValue)) {
                $parameter = $parameterValue + $this->parameters;
            } else {
                $parameter[] = $parameterValue;
            }
        }
        return route($this->route, $parameter);
    }
}

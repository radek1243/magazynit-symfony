<?php

namespace App\Converters;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;

class TypeConverter extends DoctrineParamConverter{

    public function apply(Request $request, ParamConverter $configuration)
    {
        $type = $request->attributes->get('type');
        if($type!==null &&  is_string($type)) {
            $request->attributes->set('type', urldecode($type));
        }
        return parent::apply($request, $configuration);
    }
}
<?php

namespace App\Converters;

use App\Communicate\CommunicateBuilder;
use App\Communicate\CommunicateValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CommunicateConverter implements ParamConverterInterface{

    public function supports(ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();
        if($name==='communicate' && $class==='string'){
            return true;
        }
        else return false;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $success = $request->attributes->get('communicate');
        if($success === null ) return false;
        $comVal = new CommunicateValidator();
        $communicate = CommunicateBuilder::build($success);
        if($comVal->isValid($communicate)){
            $request->attributes->set('communicate',$communicate);
            return true;
        }
        return false;
    }
}
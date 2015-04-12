<?php

namespace UserLib\Product;

class Phone implements ProductInterface
{
    use GpsTrait;

    public function getName()
    {
        return 'phone';
    }
}

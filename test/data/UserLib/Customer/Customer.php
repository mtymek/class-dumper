<?php

namespace UserLib\Customer;

use UserLib\Admin\Admin;
use UserLib\UserInterface;
use UserLib\Product\ProductInterface;

class Customer implements CustomerInterface, UserInterface
{
    public function buy(ProductInterface $product)
    {
    }

    public function sendMessageToAdmin(Admin $admin)
    {
    }
}

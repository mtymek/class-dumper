<?php

namespace UserLib\Customer;

use UserLib\Admin\Admin;
use UserLib\Product\ProductInterface as Product;

interface CustomerInterface
{
    public function buy(Product $product);

    public function sendMessageToAdmin(Admin $admin);
}

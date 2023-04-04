<?php
namespace App\Services\Address;

interface AddressService
{
    public function getAddresses() : array;
    public function getAddress($addressId) : array;
    public function createAddress($attr) : array;
    public function updateAddress($addressId, $attr) : array;
    public function removeAddress($addressId) : array;
    public function selectAddress($addressId) : array;
}

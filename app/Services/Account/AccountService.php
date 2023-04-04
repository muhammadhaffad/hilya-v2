<?php
namespace App\Services\Account;

interface AccountService
{
    public function account();
    public function updateProfile($attr);
    public function changePassword($attr);
}
?>
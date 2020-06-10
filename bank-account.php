<?php

interface Banking
{
    public function deposit(int $amount);
    public function withdraw(int $amount);
}

abstract class BankAccount implements Banking {
    const NEGATIVE_LIMIT = -100;

    protected $balance;
    protected $isBlocked;

    public function __construct() {
        $this->balance = 0;
        $this->isBlocked = false;
    }

    public function deposit(int $amount)
    {
        $this->balance += $amount;

        if ($this->isBlocked && $this->balance >= 0) {
            $this->isBlocked = false;
        }
    }

    public function withdraw(int $amount) {
        if ($this->isBlocked) {
            return;
        }

        $this->balance -= $amount;

        if ($this->balance <= static::NEGATIVE_LIMIT) {
            $this->isBlocked = true;
        }
    }

    public function getBalance() {
        return $this->balance;
    }

    public function isBlocked() {
        return $this->isBlocked;
    }
}

class SimpleBankAccount extends BankAccount {
    const NEGATIVE_LIMIT = -200;
}

class SecuredBankAccount extends BankAccount {
    const NEGATIVE_LIMIT = -2000;

    public function deposit(int $amount) {
        $actualAmount = $amount - $amount * 0.025;

        parent::deposit($actualAmount);
    }

    public function withdraw(int $amount) {
        $actualAmount = $amount - $amount * 0.025;

        parent::withdraw($actualAmount);
    }
}

class User implements Banking
{
    public $firstName;
    public $lastName;
    private $bankAccount;

    public function __construct(string $firstName, string $lastName) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function chooseBankAccount(BankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function deposit(int $amount)
    {
        $this->bankAccount->deposit($amount);
    }

    public function withdraw(int $amount)
    {
        $this->bankAccount->withdraw($amount);
    }
}


$simpleBankAccount = new SimpleBankAccount();
$securedBankAccount = new SecuredBankAccount();

$user = new User('Petar', 'Petrovic');

$user->chooseBankAccount($simpleBankAccount);
$user->deposit(400);
$user->withdraw(450);

echo '<pre>';
var_dump($simpleBankAccount);
var_dump($securedBankAccount);

$user->chooseBankAccount($securedBankAccount);
$user->deposit(400);
$user->withdraw(450);

echo '<pre>';
var_dump($simpleBankAccount);
var_dump($securedBankAccount);
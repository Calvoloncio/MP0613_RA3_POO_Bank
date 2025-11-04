<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:39 PM
 */

/**
 * @description: Grant 100.00 overdraft funds.
 * */
class SilverOverdraft 
{
    protected float $overdraftFunds;

    public function __construct(float $limite = 100.0)
    {
        $this->overdraftFunds = $limite;
    }

    public function getOverdraftFunds(): float
    {
        return $this->overdraftFunds;
    }
    public function isGrantOverdraftFunds(float $newAmount): bool
    {
        return false;
    }    

    

}

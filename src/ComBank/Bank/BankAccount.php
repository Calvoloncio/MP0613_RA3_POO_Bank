<?php namespace ComBank\Bank;
 
/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */
 
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use function PHPUnit\Framework\throwException;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
 
 
class BankAccount implements BankAccountInterface
{
    private float $balance;
    private string $status;
    private OverdraftInterface $overdraftStrategy;
 
    // Constructor
    public function __construct(float $initialBalance = 0.0, OverdraftInterface $overdraftStrategy = null)
    {
        $this->balance = $initialBalance;
        $this->status = 'OPEN';
        $this->overdraftStrategy = $overdraftStrategy ?? new NoOverdraft();
    }
 
 
    public function getOverdraftStrategy(): OverdraftInterface
    {
        return $this->overdraftStrategy;
    }
 
    public function newB(float $initialBalance = 0.0): void
    {
        $this->balance = $initialBalance;
        $this->status = 'OPEN';
        echo "Nueva cuenta creada con balance:". $this->balance ."€";
    }
 
    public function isOpen(): bool
    {
        if ($this->status === 'OPEN') {
            return true;
        }
        else {
            return false;
        }
    }
   
    public function closeAccount(): void
    {
        if ($this->status === 'CLOSED') {
            throw new BankAccountException("La cuenta ya está cerrada.");
        }
        $this->status = 'CLOSED';
    }
   
    public function reopenAccount(): void
    {
        if ($this->status === 'OPEN') {
            throw new BankAccountException("La cuenta ya está abierta.");
        }else{
            $this->status = 'OPEN';
        }
    }
 
    public function getBalance(): float
    {
        return $this->balance;
    }
 
    public function setBalance(float $newBalance): void
    {
        $this->balance = $newBalance;
    }
 
    public function applyOverdraft(OverdraftInterface $overdraftStrategy): void
    {
        $this->overdraftStrategy = $overdraftStrategy;
    }
 
    public function transaction(BankTransactionInterface $bankTransaction) : void
    {
    
    if ($this->status !== 'OPEN'){
        echo"No se puede realizar la transacción: la cuenta está cerrada.";
    }
 
    $amount = $bankTransaction->getAmount();
 
    if ($bankTransaction instanceof WithdrawTransaction) {
        $newAmount = $this->balance - $amount;
        if ($this->overdraftStrategy->isGrantOverdraftFunds($newAmount) == false) {
            throw new FailedTransactionException("Fondos insuficientes o límite de descubierto excedido para la transacción.<br><br>");
        }
    }
    try {
        $newBalance = $bankTransaction->applyTransaction($this);
        $this->setBalance($newBalance);
 
    } catch (InvalidOverdraftFundsException $e) {
        echo"Transacción fallida: ";
    } catch (\Throwable $e) {
        throw new FailedTransactionException("Error inesperado durante la transacción: " . $e->getMessage());
    }
}
}
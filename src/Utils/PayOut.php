<?php
namespace App\Utils;

use Webmozart\Assert\InvalidArgumentException;

class PayOut
{
    private $transactionAmount;
    private $transactionCurrency;
    private $transactionReason;
    private $appTransactionRef;
    private $customerPhoneNumber;
    private $customerName;
    private $customerEmail;
    private $customerLang;
    private $transactionOperator;
    private $transaction_receiver;


    public function __construct(
        $transactionAmount = null,
        $transactionCurrency = null,
        $transactionReason = null,
        $appTransactionRef = null,
        $customerPhoneNumber = null,
        $customerName = null,
        $customerEmail = null,
        $transactionOperator = null,
        $customerLang = null
        ,  $transaction_receiver=null
    ) {
        $this->transactionAmount = $transactionAmount;
        $this->transactionCurrency = $transactionCurrency;
        $this->transactionReason = $transactionReason;
        $this->appTransactionRef = $appTransactionRef;
        $this->customerPhoneNumber = $customerPhoneNumber;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->transactionOperator = $transactionOperator;
        $this->customerLang = $customerLang;
        $this->transaction_receiver = $transaction_receiver;
    }

    public function getTransactionOperator(): ?string
    {
        return $this->transactionOperator;
    }

    // Setter method for transactionOperator  CM_OM for orange Money or CM_MOMO for MTN Money
    public function setTransactionOperator(?string $transactionOperator): void
    {
        if ($transactionOperator === "CM_OM" || $transactionOperator === "CM_MOMO") {
            $this->transactionOperator = $transactionOperator;
        } else {
            throw new InvalidArgumentException("Invalid transaction operator. Only 'CM_OM' or 'CM_MOMO' are allowed.");
        }
    }
    public function getTransactionAmount(): ?float
    {
        return $this->transactionAmount;
    }

    public function setTransactionAmount(?float $transactionAmount): void
    {
        $this->transactionAmount = $transactionAmount;
    }

    public function getTransactionCurrency(): ?string
    {
        return $this->transactionCurrency;
    }

    public function setTransactionCurrency(?string $transactionCurrency): void
    {
        $this->transactionCurrency = $transactionCurrency;
    }

    public function getTransactionReason(): ?string
    {
        return $this->transactionReason;
    }

    public function setTransactionReason(?string $transactionReason): void
    {
        $this->transactionReason = $transactionReason;
    }

    public function getAppTransactionRef(): ?string
    {
        return $this->appTransactionRef;
    }

    public function setAppTransactionRef(?string $appTransactionRef): void
    {
        $this->appTransactionRef = $appTransactionRef;
    }

    public function getCustomerPhoneNumber(): ?string
    {
        return $this->customerPhoneNumber;
    }

    public function setCustomerPhoneNumber(?string $customerPhoneNumber): void
    {
        $this->customerPhoneNumber = $customerPhoneNumber;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(?string $customerName): void
    {
        $this->customerName = $customerName;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): void
    {
        $this->customerEmail = $customerEmail;
    }

    public function getCustomerLang(): ?string
    {
        return $this->customerLang;
    }

    public function setCustomerLang(?string $customerLang): void
    {
        $this->customerLang = $customerLang;
    }

    public function getTransactionReceiver()
    {
        return $this->transaction_receiver;
    }

    public function setTransactionReceiver($transaction_receiver)
    {
        $this->transaction_receiver = $transaction_receiver;
    }

    public function toJson(): string
    {
        $data = [
            "transaction_amount" => $this->transactionAmount,
            "transaction_currency" => $this->transactionCurrency,
            "transaction_reason" => $this->transactionReason,
            "app_transaction_ref" => $this->appTransactionRef,
            "customer_phone_number" => $this->customerPhoneNumber,
            "customer_name" => $this->customerName,
            "customer_email" => $this->customerEmail,
            "customer_lang" => $this->customerLang,
            "transaction_operator" => $this->transactionOperator,
            "transaction_receiver"=>$this->transaction_receiver

        ];

        return json_encode($data);
    }
}
<?php
namespace App\Utils;

class PayIn
{
    private $transactionAmount;
    private $transactionCurrency;
    private $transactionReason;
    private $appTransactionRef;
    private $customerPhoneNumber;
    private $customerName;
    private $customerEmail;
    private $customerLang;
    private $transaction_receiver;
    private $number_operator;


    public function __construct(
        $transactionAmount = null,
        $transactionCurrency = null,
        $transactionReason = null,
        $appTransactionRef = null,
        $customerPhoneNumber = null,
        $customerName = null,
        $customerEmail = null,
        $customerLang = null,
        $transaction_receiver = null,
        $number_operator = null
    ) {
        $this->transactionAmount = $transactionAmount;
        $this->transactionCurrency = $transactionCurrency;
        $this->transactionReason = $transactionReason;
        $this->appTransactionRef = $appTransactionRef;
        $this->customerPhoneNumber = $customerPhoneNumber;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->customerLang = $customerLang;
        $this->transaction_receiver = $transaction_receiver;
        $this->number_operator = $number_operator;

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

    public function setNumberOperator($number_operator)
    {
        $this->number_operator = $number_operator;
    }

    public function getNumberOperator()
    {
        return $this->number_operator;
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
            "transaction_receiver" => $this->transaction_receiver,
            "transaction_operator" => $this->number_operator
        ];

        return json_encode($data);
    }
}
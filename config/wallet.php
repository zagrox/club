<?php

return [
    /**
     * Deposit transactions: when the funds were received.
     */
    'deposit' => [
        'type' => 'deposit',

        'meta' => [
            'title' => 'Deposit',
            'description' => 'Top-up account',
        ],
    ],

    /**
     * Withdraw transactions: when withdrawing funds.
     */
    'withdraw' => [
        'type' => 'withdraw',

        'meta' => [
            'title' => 'Withdraw',
            'description' => 'Withdrawal',
        ],
    ],

    /**
     * These transfers add balance to the receiving wallet.
     */
    'transfer' => [
        'type' => 'transfer',

        'meta' => [
            'title' => 'Transfer',
            'description' => 'Account transfer',
        ],
    ],
 
    /**
     * These transfers subtract balance from the sending wallet.
     */
    'withdraw-transfer' => [
        'type' => 'withdraw_transfer',

        'meta' => [
            'title' => 'Transfer',
            'description' => 'Account transfer',
        ],
    ],

    /**
     * Exchange transactions, when exchanging one currency for another.
     */
    'exchange' => [
        'type' => 'exchange',

        'meta' => [
            'title' => 'Exchange',
            'description' => 'Exchange between wallets',
        ],
    ],

    /**
     * Transactions, when the transfer is rejected.
     */
    'refund' => [
        'type' => 'refund',

        'meta' => [
            'title' => 'Refund',
            'description' => 'Refund operation',
        ],
    ],

    /**
     * Transactions when purchasing a product/service.
     */
    'purchase' => [
        'type' => 'purchase',

        'meta' => [
            'title' => 'Purchase',
            'description' => 'Purchase of goods/services',
        ],
    ],

    /**
     * Models for mapping.
     */
    'mapping' => [
        'transaction' => \Bavix\Wallet\Models\Transaction::class,
        'transfer' => \Bavix\Wallet\Models\Transfer::class,
        'wallet' => \Bavix\Wallet\Models\Wallet::class,
    ],

    /**
     * The model is used to store currencies.
     */
    'currency' => [
        'model' => null,
        'id' => null,
    ],

    /**
     * Powerful feature of this package.
     * If you enable this option, all the exchanges that you have
     * made in past and going to make in future will be logged.
     * This option is monitoring command to start and stop the exchange process.
     */
    'exchanges' => [
        'logger' => [
            'enabled' => true,
            'model' => \Bavix\Wallet\Models\ExchangeLog::class,
            'table' => 'exchange_logs',
        ],
    ],

    /**
     * Sometimes a slug may not be enough and you need to create
     * your wallet with an additional type.
     * Set 'default' to true to use UUID for all wallets by default.
     *
     * @see https://github.com/bavix/laravel-wallet/discussions/254
     */
    'uuid' => [
        'default' => false,
        'model' => \Bavix\Wallet\Models\WalletUuid::class,
        'table' => 'wallet_uuids',
        'type' => 'uuid', // string
    ],

    /**
     * Lock settings.
     */
    'lock' => [
        'enabled' => false,
        'seconds' => 1,
    ],
]; 
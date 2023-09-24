const db = connect("pay-dev");
console.log(db.stats);

const sampleTransaction = {
    created: "2018-11-14T06:00:22.700Z",
    updated: "2021-04-23T02:00:23.322Z",
    updatedKeys: [],
    deleted: false,
    amount: 11000,
    merchantId: ObjectId("5bebb2e27f91159861846d6a"),
    autocreditItemId: null,
    transactionReportId: ObjectId("5bec70dd702833f54cfcdf18"),
    currency: "GBP",
    provider: "card",
    state: "CONFIRMED",
    qr: {
        url: "https://s3.eu-west-1.amazonaws.com/pay-qr-codes-rto4scw3-dev/5bebb2e27f91159861846d6a/transactions/5bebb9f6a110ff5c5831b966TQrCode.png",
    },
    securityWord: "ALLEGED HAWK",
    canRefundIfConfirmed: true,
    calculateFeeOnImport: false,
    externalImport: false,
    externalImportReference: null,
    merchantDiscountRateCode: "AGNOUS",
    externalId: "",
    includeInAutocredit: true,
    paymentToken: null,
    history: [
        {
            state: "INITIATED",
            updatedDate: "2018-11-14T06:00:22.700Z",
            trigger: "SYSTEM",
        },
        {
            state: "QR_CODE_GENERATED",
            updatedDate: "2018-11-14T06:00:28.934Z",
            trigger: "SYSTEM",
        },
        {
            state: "CONFIRMED",
            updatedDate: "2018-11-14T09:48:23.791Z",
            trigger: "VENDOR_WEBHOOK",
        },
        {
            state: "REMITTED",
            updatedDate: "2021-04-23T02:00:23.322Z",
            trigger: "SYSTEM",
            detailsPath: "remittance-history",
            detailsId: ObjectId("60822a3712246a0008aef90d"),
        },
    ],
    appVersion: "1.0.0",
    apiVersion: "2.0.0",
    deviceId: "4d10df79-87d2-4d1e-879a-1e60c38afd64",
    redirectUrl: null,
    costStructure: {
        currency: "GBP",
        feeRateExact: 1165,
        rateFee: 1165,
        fee: 1165,
        payable: 9835,
        fixedFee: 0,
        merchantDiscountRate: {
            fixedFee: 0,
            rateFee: 665,
            code: "AGNOUS",
            provider: "bml_epos",
            resellerMetadata: {
                reseller: " Pay",
                resellerCode: "AGNOUS",
            },
        },
    },
    providerDisplayName: null,
    remittanceId: ObjectId("60822a3712246a0008aef90d"),
    orderInfo: null,
    originatorApp: "4d10df79-87d2-4d1e-879a-1e60c38afd64",
    refundRemittance: null,
    refundId: null,
    refundReason: null,
    redirectSecret: null,
    webhooksReceived: [
        '"{\\n  \\"id\\": \\"evt_1DWKywLGetorKlgqmgZ4P4TG\\",\\n  \\"object\\": \\"event\\",\\n  \\"api_version\\": \\"2018-02-28\\",\\n  \\"created\\": 1542188902,\\n  \\"data\\": {\\n    \\"object\\": {\\n      \\"id\\": \\"ch_1DWKywLGetorKlgqXENmh6O5\\",\\n      \\"object\\": \\"charge\\",\\n      \\"amount\\": 10000,\\n      \\"amount_refunded\\": 0,\\n      \\"application\\": null,\\n      \\"application_fee\\": null,\\n      \\"balance_transaction\\": \\"txn_1DWKywLGetorKlgqwuMgWCxe\\",\\n      \\"captured\\": true,\\n      \\"created\\": 1542188902,\\n      \\"currency\\": \\"gbp\\",\\n      \\"customer\\": null,\\n      \\"description\\": null,\\n      \\"destination\\": null,\\n      \\"dispute\\": null,\\n      \\"failure_code\\": null,\\n      \\"failure_message\\": null,\\n      \\"fraud_details\\": {\\n      },\\n      \\"invoice\\": null,\\n      \\"livemode\\": false,\\n      \\"metadata\\": {\\n        \\"id\\": \\"5bebb9f6a110ff5c5831b966\\",\\n        \\"merchant\\": \\"5bebb2e27f91159861846d6a\\"\\n      },\\n      \\"on_behalf_of\\": null,\\n      \\"order\\": null,\\n      \\"outcome\\": {\\n        \\"network_status\\": \\"approved_by_network\\",\\n        \\"reason\\": null,\\n        \\"risk_level\\": \\"normal\\",\\n        \\"risk_score\\": 6,\\n        \\"seller_message\\": \\"Payment complete.\\",\\n        \\"type\\": \\"authorized\\"\\n      },\\n      \\"paid\\": true,\\n      \\"payment_intent\\": null,\\n      \\"receipt_email\\": null,\\n      \\"receipt_number\\": null,\\n      \\"refunded\\": false,\\n      \\"refunds\\": {\\n        \\"object\\": \\"list\\",\\n        \\"data\\": [\\n\\n        ],\\n        \\"has_more\\": false,\\n        \\"total_count\\": 0,\\n        \\"url\\": \\"/v1/charges/ch_1DWKywLGetorKlgqXENmh6O5/refunds\\"\\n      },\\n      \\"review\\": null,\\n      \\"shipping\\": null,\\n      \\"source\\": {\\n        \\"id\\": \\"card_1DWKyuLGetorKlgqsumwivpH\\",\\n        \\"object\\": \\"card\\",\\n        \\"address_city\\": null,\\n        \\"address_country\\": null,\\n        \\"address_line1\\": null,\\n        \\"address_line1_check\\": null,\\n        \\"address_line2\\": null,\\n        \\"address_state\\": null,\\n        \\"address_zip\\": null,\\n        \\"address_zip_check\\": null,\\n        \\"brand\\": \\"MasterCard\\",\\n        \\"country\\": \\"GB\\",\\n        \\"customer\\": null,\\n        \\"cvc_check\\": null,\\n        \\"dynamic_last4\\": \\"4263\\",\\n        \\"exp_month\\": 9,\\n        \\"exp_year\\": 2021,\\n        \\"fingerprint\\": \\"dlozblGnKZ21pCw7\\",\\n        \\"funding\\": \\"debit\\",\\n        \\"last4\\": \\"7110\\",\\n        \\"metadata\\": {\\n        },\\n        \\"name\\": null,\\n        \\"tokenization_method\\": \\"apple_pay\\"\\n      },\\n      \\"source_transfer\\": null,\\n      \\"statement_descriptor\\": null,\\n      \\"status\\": \\"succeeded\\",\\n      \\"transfer_group\\": null\\n    }\\n  },\\n  \\"livemode\\": false,\\n  \\"pending_webhooks\\": 3,\\n  \\"request\\": {\\n    \\"id\\": \\"req_jCBlxhZjTASKgU\\",\\n    \\"idempotency_key\\": null\\n  },\\n  \\"type\\": \\"charge.succeeded\\"\\n}"',
    ],
    webhooksSent: [],
    vendor: "stripe",
    vendorResponses: [],
    vendorQrCode: null,
    metadata: null,
    resellerMetadata: {},
    subTotal: null,
    taxesTotal: null,
    serviceChargeTotal: null,
    paymentLinks: [],
    initiator: "478904e4-117d-4240-a189-b6aeb20e652c",
    url: "https://transaction.dev.pay.com/5bebb9f6a110ff5c5831b966",
    trigger: null,
    platformBilling: {
        mdrBilling: {
            id: ObjectId("5ecc939465bdb318a512fb67"),
            created: "2021-01-27T03:01:56.288Z",
            updated: "2021-01-27T03:01:56.288Z",
            updatedKeys: [],
            deleted: false,
            mdr: "AGNOUS",
            fixedFeeCurrency: null,
            remittance: true,
            processingFixed: 0,
            processingPercentage: 1.89,
            platformFixed: 0,
            platformPercentage: 0.35,
            __uniqueID: 1611716516123.0,
        },
        platformFeeCurrency: "GBP",
        platformFixedFee: 0,
        platformPercentageFee: 38.5,
        processingFeeCurrency: "GBP",
        processingPercentageFee: 207.9,
        processingFixedFee: 0,
        remittance: true,
        processingFeeTotal: 207.9,
        platformFeeTotal: 38.5,
        remittanceCurrency: "GBP",
        remittanceTotal: 10753.6,
        totalFee: 246.4,
    },
    platformBillingReportId: ObjectId("6010d50f8ffedf00084c6ce5"),
    accountingState: "REMITTED",
    availableBalance: 11000.0,
};
const n = 10 * 1000 * 1000;
const batchSize = 100 * 1000;

function randomStatus() {
    const statuses = [
        "AUTHORIZED",
        "QR_CODE_GENERATED",
        "CONFIRMED",
        "CANCELLED",
        "VOIDED",
        "FAILED",
        "REFUNDED",
        "REFUND_REQUESTED",
        "REMITTED",
        "INITIATED",
    ];

    return statuses[Math.floor(statuses.length * Math.random())];
}

function getRandomTransaction() {
    return Object.assign({}, sampleTransaction, { state: randomStatus() });
}

let batch = [];

for (let i = 0; i < n; ++i) {
    batch.push(getRandomTransaction());

    if (i !== 0 && i % batchSize === 0) {
        db.transactions.insertMany(batch);
        batch = [];
    }
}

db.transactions.insertMany(batch);
batch = [];

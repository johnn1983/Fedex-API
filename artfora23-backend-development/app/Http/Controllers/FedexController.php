<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fedex\PostalCodeValidationRequest;
use App\Http\Requests\Fedex\ShippingRateRequest;
use App\Models\Product;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use League\ISO3166\ISO3166;

class FedexController extends Controller
{
    private $client;
    public $account_number;
    public $meter_number;
    public $client_id;
    public $api_key;
    public $client_secret;

    public function __construct()
    {
        $this->client_id = config('services.fedex.client_id');
        $this->client_secret = config('services.fedex.client_secret');
        $this->account_number = config('services.fedex.account_number');
        $this->client = new Client([
            'base_uri' => 'https://apis-sandbox.fedex.com/', // Replace with the FedEx API base URL
            'timeout' => 10,
        ]);
    }
    public function getAccessToken()
    {
        $grant_type = 'client_credentials';

        try {
            $response = $this->client->post('oauth/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            return $responseBody;
        } catch (RequestException $e) {
            return;
        }
    }
    public function countries()
    {
        $countryClient = new Client([
            'base_uri' => 'https://api.fedex.com/country/v2/countries', // Replace with the FedEx API base URL
            'timeout' => 10,
        ]);

        try {
            $response = $countryClient->get('', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer l7xx8daef6fbb1724f21b2ada537e059ad7b',
                ],
                'query' => [
                    '_' => '-1689800400',
                    'type' => 'recipient',
                ],

            ]);

            $responseBody = json_decode($response->getBody(), true);

            return $responseBody;
        } catch (RequestException $e) {
            return $e;
        }
    }
    public function getCountryCode($countryName)
    {
        $iso3166 = new ISO3166();
        $countryData = $iso3166->name($countryName);

        $countryCode = $countryData['alpha2'];

        return $countryCode;
    }

    public function addressValidation(Request $request)
    {
        $auth = $this->getAccessToken();
        $token = $auth['access_token'];
        // return ($token);
        try {
            $response = $this->client->request('POST', 'address/v1/addresses/resolve?_=-800668409', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                // 'query' => ['_' => '-' . $this->account_number],
                'body' => json_encode([
                    'addressesToValidate' => [
                        [
                            'address' => [
                                'streetLines' => [$request->input('address'), $request->input('address2')],
                                'city' => $request->input('city'),
                                'stateOrProvinceCode' => $request->input('state'),
                                'postalCode' => $request->input('postal_code'),
                                'countryCode' => $request->input('code'),
                            ],
                        ],
                    ],
                ]),
            ]);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 200) {
                $responseData = json_decode($response->getBody(), true);
                if (count($responseData['output']['resolvedAddresses'][0]['customerMessages']) == 0) {
                    $result = json_encode(true);
                    return $result;
                } else {
                    $result = json_encode(false);
                    return $result;
                }
            } else {
                $result = json_encode(false);
            }
            return $result;
        } catch (RequestException $e) {
            $result = json_encode(false);
        }
        $result = json_encode(false);
        return $result;
    }

    public function postalCodeValidation(PostalCodeValidationRequest $request)
    {
        $auth = $this->getAccessToken();
        $token = $auth['access_token'];
        try {
            $response = $this->client->request('POST', 'country/v1/postal/validate', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    // 'x-customer-transaction-id' => $transactionId,

                ],
                'query' => [
                    'carrierCode' => ["FXCC"],
                    'countryCode' => $request->input('country_code'), // 'US
                    'postalCode' => $request->input('postal_code'),
                    'stateOrProvinceCode' => $request->input('state'),
                    'shipDate' => '2020-12-25',

                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($statusCode == 200) {
                return response()->json(['message' => 'Postal code is valid']);
            } else {
                return response()->json(['message' => 'Postal code is invalid']);
            }
        } catch (RequestException $e) {
            return response()->json(['message' => 'An error occurred while validating the postal code']);
        }
    }

    public function shipRate(ShippingRateRequest $request)
    {
        $data = $request->onlyValidated();
        $data['user_id'] = $request->user()->id;
        $auth = $this->getAccessToken();
        $token = $auth['access_token'];
        $product = Product::where('id', $data['product_id'])->first();
        $seller = User::where('id', $product->user_id)->first();
        $buyer = User::where('id', $data['user_id'])->first();
        $count = $data['count'];
        $isDelivery = $buyer->dev_email ? true : false;

        try {
            $response = $this->client->request('POST', 'rate/v1/rates/quotes', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    '_' => '-' . $this->account_number,
                    'type' => 'recipient',
                ],
                'body' => json_encode([
                    "accountNumber" => [
                        "value" => "201400044"
                    ],
                    "rateRequestControlParameters" => [
                        "returnTransitTimes" => false,
                        "servicesNeededOnRateFailure" => true,
                        "variableOptions" => "FREIGHT_GUARANTEE",
                        "rateSortOrder" => "SERVICENAMETRADITIONAL"
                    ],
                    "requestedShipment" => [
                        "shipper" => [
                            "address" => [
                                "streetLines" => [
                                    "1550 Union Blvd",
                                    "Suite 302"
                                ],
                                "city" => "Beverly Hills",
                                "stateOrProvinceCode" => "TN",
                                "postalCode" => "65247",
                                "countryCode" => "US",
                                "residential" => false
                            ]
                        ],
                        "recipient" => [
                            "address" => [
                                "streetLines" => [
                                    "1550 Union Blvd",
                                    "Suite 302"
                                ],
                                "city" => "Beverly Hills",
                                "stateOrProvinceCode" => "TN",
                                "postalCode" => "65247",
                                "countryCode" => "US",
                                "residential" => false
                            ]
                        ],
                        "serviceType" => "STANDARD_OVERNIGHT",
                        "emailNotificationDetail" => [
                            "recipients" => [
                                [
                                    "emailAddress" => "string",
                                    "notificationEventType" => [
                                        "ON_DELIVERY"
                                    ],
                                    "smsDetail" => [
                                        "phoneNumber" => "string",
                                        "phoneNumberCountryCode" => "string"
                                    ],
                                    "notificationFormatType" => "HTML",
                                    "emailNotificationRecipientType" => "BROKER",
                                    "notificationType" => "EMAIL",
                                    "locale" => "string"
                                ]
                            ],
                            "personalMessage" => "string",
                            "PrintedReference" => [
                                "printedReferenceType" => "BILL_OF_LADING",
                                "value" => "string"
                            ]
                        ],
                        "preferredCurrency" => "USD",
                        "rateRequestType" => [
                            "ACCOUNT",
                            "LIST"
                        ],
                        "shipDateStamp" => "2019-09-05",
                        "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                        "requestedPackageLineItems" => [
                            [
                                "subPackagingType" => "BAG",
                                "groupPackageCount" => 1,
                                "contentRecord" => [
                                    [
                                        "itemNumber" => "string",
                                        "receivedQuantity" => 0,
                                        "description" => "string",
                                        "partNumber" => "string"
                                    ]
                                ],
                                "declaredValue" => [
                                    "amount" => "100",
                                    "currency" => "USD"
                                ],
                                "weight" => [
                                    "units" => "LB",
                                    "value" => 22
                                ],
                                "dimensions" => [
                                    "length" => 10,
                                    "width" => 8,
                                    "height" => 2,
                                    "units" => "IN"
                                ],
                                "variableHandlingChargeDetail" => [
                                    "rateType" => "ACCOUNT",
                                    "percentValue" => 0,
                                    "rateLevelType" => "BUNDLED_RATE",
                                    "fixedValue" => [
                                        "amount" => "100",
                                        "currency" => "USD"
                                    ],
                                    "rateElementBasis" => "NET_CHARGE"
                                ],
                                "packageSpecialServices" => [
                                    "specialServiceTypes" => [
                                        "DANGEROUS_GOODS"
                                    ],
                                    "signatureOptionType" => [
                                        "NO_SIGNATURE_REQUIRED"
                                    ],
                                    "alcoholDetail" => [
                                        "alcoholRecipientType" => "LICENSEE",
                                        "shipperAgreementType" => "Retailer"
                                    ],
                                    "dangerousGoodsDetail" => [
                                        "offeror" => "Offeror Name",
                                        "accessibility" => "ACCESSIBLE",
                                        "emergencyContactNumber" => "3268545905",
                                        "options" => [
                                            "BATTERY"
                                        ],
                                        "containers" => [
                                            [
                                                "offeror" => "Offeror Name",
                                                "hazardousCommodities" => [
                                                    [
                                                        "quantity" => [
                                                            "quantityType" => "GROSS",
                                                            "amount" => 0,
                                                            "units" => "LB"
                                                        ],
                                                        "innerReceptacles" => [
                                                            [
                                                                "quantity" => [
                                                                    "quantityType" => "GROSS",
                                                                    "amount" => 0,
                                                                    "units" => "LB"
                                                                ]
                                                            ]
                                                        ],
                                                        "options" => [
                                                            "labelTextOption" => "Override",
                                                            "customerSuppliedLabelText" => "LabelText"
                                                        ],
                                                        "description" => [
                                                            "sequenceNumber" => 0,
                                                            "processingOptions" => [
                                                                "INCLUDE_SPECIAL_PROVISIONS"
                                                            ],
                                                            "subsidiaryClasses" => "subsidiaryClass",
                                                            "labelText" => "labelText",
                                                            "technicalName" => "technicalName",
                                                            "packingDetails" => [
                                                                "packingInstructions" => "instruction",
                                                                "cargoAircraftOnly" => false
                                                            ],
                                                            "authorization" => "Authorization Information",
                                                            "reportableQuantity" => false,
                                                            "percentage" => 10,
                                                            "id" => "ID",
                                                            "packingGroup" => "DEFAULT",
                                                            "properShippingName" => "ShippingName",
                                                            "hazardClass" => "hazardClass"
                                                        ]
                                                    ]
                                                ],
                                                "numberOfContainers" => 10,
                                                "containerType" => "Copper Box",
                                                "emergencyContactNumber" => [
                                                    "areaCode" => "202",
                                                    "extension" => "3245",
                                                    "countryCode" => "US",
                                                    "personalIdentificationNumber" => "9545678",
                                                    "localNumber" => "23456"
                                                ],
                                                "packaging" => [
                                                    "count" => 20,
                                                    "units" => "Liter"
                                                ],
                                                "packingType" => "ALL_PACKED_IN_ONE",
                                                "radioactiveContainerClass" => "EXCEPTED_PACKAGE"
                                            ]
                                        ],
                                        "packaging" => [
                                            "count" => 20,
                                            "units" => "Liter"
                                        ]
                                    ],
                                    "packageCODDetail" => [
                                        "codCollectionAmount" => [
                                            "amount" => 12.45,
                                            "currency" => "USD"
                                        ],
                                        "codCollectionType" => "ANY"
                                    ],
                                    "pieceCountVerificationBoxCount" => 0,
                                    "batteryDetails" => [
                                        [
                                            "material" => "LITHIUM_METAL",
                                            "regulatorySubType" => "IATA_SECTION_II",
                                            "packing" => "CONTAINED_IN_EQUIPMENT"
                                        ]
                                    ],
                                    "dryIceWeight" => [
                                        "units" => "LB",
                                        "value" => 10
                                    ]
                                ]
                            ]
                        ],
                        "documentShipment" => false,
                        "variableHandlingChargeDetail" => [
                            "rateType" => "ACCOUNT",
                            "percentValue" => 0,
                            "rateLevelType" => "BUNDLED_RATE",
                            "fixedValue" => [
                                "amount" => "100",
                                "currency" => "USD"
                            ],
                            "rateElementBasis" => "NET_CHARGE"
                        ],
                        "packagingType" => "YOUR_PACKAGING",
                        "totalPackageCount" => 3,
                        "totalWeight" => 87.5,
                        "shipmentSpecialServices" => [
                            "returnShipmentDetail" => [
                                "returnType" => "PRINT_RETURN_LABEL"
                            ],
                            "deliveryOnInvoiceAcceptanceDetail" => [
                                "recipient" => [
                                    "accountNumber" => [
                                        "value" => 201400044
                                    ],
                                    "address" => [
                                        "streetLines" => [
                                            "10 FedEx Parkway",
                                            "Suite 30"
                                        ],
                                        "countryCode" => "US"
                                    ],
                                    "contact" => [
                                        "companyName" => "FedEx",
                                        "faxNumber" => "9013577890",
                                        "personName" => "John Taylor",
                                        "phoneNumber" => "9013577890"
                                    ]
                                ]
                            ],
                            "internationalTrafficInArmsRegulationsDetail" => [
                                "licenseOrExemptionNumber" => "432345"
                            ],
                            "pendingShipmentDetail" => [
                                "pendingShipmentType" => "EMAIL",
                                "processingOptions" => [
                                    "options" => [
                                        "ALLOW_MODIFICATIONS"
                                    ]
                                ],
                                "recommendedDocumentSpecification" => [
                                    "types" => [
                                        "ANTIQUE_STATEMENT_EUROPEAN_UNION"
                                    ]
                                ],
                                "emailLabelDetail" => [
                                    "recipients" => [
                                        [
                                            "emailAddress" => "string",
                                            "optionsRequested" => [
                                                "options" => [
                                                    "PRODUCE_PAPERLESS_SHIPPING_FORMAT"
                                                ]
                                            ],
                                            "role" => "SHIPMENT_COMPLETOR",
                                            "locale" => [
                                                "country" => "string",
                                                "language" => "string"
                                            ]
                                        ]
                                    ],
                                    "message" => "string"
                                ],
                                "documentReferences" => [
                                    [
                                        "documentType" => "CERTIFICATE_OF_ORIGIN",
                                        "customerReference" => "string",
                                        "description" => "ShippingDocumentSpecification",
                                        "documentId" => "98123"
                                    ]
                                ],
                                "expirationTimeStamp" => "2012-12-31",
                                "shipmentDryIceDetail" => [
                                    "totalWeight" => [
                                        "units" => "LB",
                                        "value" => 10
                                    ],
                                    "packageCount" => 12
                                ]
                            ],
                            "holdAtLocationDetail" => [
                                "locationId" => "YBZA",
                                "locationContactAndAddress" => [
                                    "address" => [
                                        "streetLines" => [
                                            "10 FedEx Parkway",
                                            "Suite 302"
                                        ],
                                        "city" => "Beverly Hills",
                                        "stateOrProvinceCode" => "CA",
                                        "postalCode" => "38127",
                                        "countryCode" => "US",
                                        "residential" => false
                                    ],
                                    "contact" => [
                                        "personName" => "person name",
                                        "emailAddress" => "email address",
                                        "phoneNumber" => "phone number",
                                        "phoneExtension" => "phone extension",
                                        "companyName" => "company name",
                                        "faxNumber" => "fax number"
                                    ]
                                ],
                                "locationType" => "FEDEX_ONSITE"
                            ],
                            "shipmentCODDetail" => [
                                "addTransportationChargesDetail" => [
                                    "rateType" => "ACCOUNT",
                                    "rateLevelType" => "BUNDLED_RATE",
                                    "chargeLevelType" => "CURRENT_PACKAGE",
                                    "chargeType" => "COD_SURCHARGE"
                                ],
                                "codRecipient" => [
                                    "accountNumber" => [
                                        "value" => 201400044
                                    ]
                                ],
                                "remitToName" => "FedEx",
                                "codCollectionType" => "ANY",
                                "financialInstitutionContactAndAddress" => [
                                    "address" => [
                                        "streetLines" => [
                                            "10 FedEx Parkway",
                                            "Suite 302"
                                        ],
                                        "city" => "Beverly Hills",
                                        "stateOrProvinceCode" => "CA",
                                        "postalCode" => "38127",
                                        "countryCode" => "US",
                                        "residential" => false
                                    ],
                                    "contact" => [
                                        "personName" => "person name",
                                        "emailAddress" => "email address",
                                        "phoneNumber" => "phone number",
                                        "phoneExtension" => "phone extension",
                                        "companyName" => "company name",
                                        "faxNumber" => "fax number"
                                    ]
                                ],
                                "returnReferenceIndicatorType" => "INVOICE"
                            ],
                            "shipmentDryIceDetail" => [
                                "totalWeight" => [
                                    "units" => "LB",
                                    "value" => 10
                                ],
                                "packageCount" => 12
                            ],
                            "internationalControlledExportDetail" => [
                                "type" => "DEA_036"
                            ],
                            "homeDeliveryPremiumDetail" => [
                                "phoneNumber" => [
                                    "areaCode" => "areaCode",
                                    "extension" => "extension",
                                    "countryCode" => "countryCode",
                                    "personalIdentificationNumber" => "personalIdentificationNumber",
                                    "localNumber" => "localNumber"
                                ],
                                "shipTimestamp" => "2020-04-24",
                                "homedeliveryPremiumType" => "APPOINTMENT"
                            ],
                            "specialServiceTypes" => [
                                "BROKER_SELECT_OPTION"
                            ]
                        ],
                        "customsClearanceDetail" => [
                            "commercialInvoice" => [
                                "shipmentPurpose" => "GIFT"
                            ],
                            "freightOnValue" => "CARRIER_RISK",
                            "dutiesPayment" => [
                                "payor" => [
                                    "responsibleParty" => [
                                        "address" => [
                                            "streetLines" => [
                                                "10 FedEx Parkway",
                                                "Suite 302"
                                            ],
                                            "city" => "Beverly Hills",
                                            "stateOrProvinceCode" => "CA",
                                            "postalCode" => "90210",
                                            "countryCode" => "US",
                                            "residential" => false
                                        ],
                                        "contact" => [
                                            "personName" => "John Taylor",
                                            "emailAddress" => "sample@company.com",
                                            "phoneNumber" => "1234567890",
                                            "phoneExtension" => "phone extension",
                                            "companyName" => "Fedex",
                                            "faxNumber" => "fax number"
                                        ],
                                        "accountNumber" => [
                                            "value" => "123456789"
                                        ]
                                    ]
                                ],
                                "paymentType" => "SENDER"
                            ],
                            "commodities" => [
                                [
                                    "description" => "DOCUMENTS",
                                    "weight" => [
                                        "units" => "LB",
                                        "value" => 22
                                    ],
                                    "quantity" => 1,
                                    "customsValue" => [
                                        "amount" => "100",
                                        "currency" => "USD"
                                    ],
                                    "unitPrice" => [
                                        "amount" => "100",
                                        "currency" => "USD"
                                    ],
                                    "numberOfPieces" => 1,
                                    "countryOfManufacture" => "US",
                                    "quantityUnits" => "PCS",
                                    "name" => "DOCUMENTS",
                                    "harmonizedCode" => "080211",
                                    "partNumber" => "P1"
                                ]
                            ]
                        ],
                        "groupShipment" => true,
                        "serviceTypeDetail" => [
                            "carrierCode" => "FDXE",
                            "description" => "string",
                            "serviceName" => "string",
                            "serviceCategory" => "string"
                        ],
                        "smartPostInfoDetail" => [
                            "ancillaryEndorsement" => "ADDRESS_CORRECTION",
                            "hubId" => "5531",
                            "indicia" => "MEDIA_MAIL",
                            "specialServices" => "USPS_DELIVERY_CONFIRMATION"
                        ],
                        "expressFreightDetail" => [
                            "bookingConfirmationNumber" => "string",
                            "shippersLoadAndCount" => 0
                        ],
                        "groundShipment" => false
                    ],
                    "carrierCodes" => [
                        "FDXE"
                    ]


                ]),


            ]);
            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($statusCode == 200) {
                return response()->json(['message' => $responseData->output->rateReplyDetails->ratedShipmentDetails->totalNetFedExCharge->amount]);
            } else {
                return response()->json(['message' => 'invalid']);
            }
        } catch (RequestException $e) {
            return response()->json(['message' => 'invalid']);

            return $e;
        }
        return response()->json(['message' => 'invalid']);
    }

    public function service_availability()
    {
        $auth = $this->getAccessToken();
        $token = $auth['access_token'];

        /*echo $token;
        die();*/
        // try {

        $response = $this->client->request('POST', 'availability/v1/transittimes', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            /* 'query' => [
                    '_' => '-' . $this->account_number,
                    'type' => 'recipient',
                ],*/
            'body' => json_encode([
                // 'countryCode' => 'US'
                "requestedShipment" => [
                    "shipper" => [
                        "address" => [
                            "city" => "Collierville",
                            "stateOrProvinceCode" => "TN",
                            "postalCode" => "38127",
                            "countryCode" => "US",
                            "residential" => false
                        ]
                    ],
                    "recipients" => [
                        [
                            "address" => [
                                "city" => "Collierville",
                                "stateOrProvinceCode" => "TN",
                                "postalCode" => "38127",
                                "countryCode" => "US",
                                "residential" => false
                            ]
                        ]
                    ],
                    "serviceType" => "FEDEX_GROUND",
                    "packagingType" => "YOUR_PACKAGING",
                    "shipDatestamp" => "2019-09-01",
                    "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                    "shippingChargesPayment" => [
                        "payor" => [
                            "responsibleParty" => [
                                "address" => [
                                    "city" => "Collierville",
                                    "stateOrProvinceCode" => "TN",
                                    "postalCode" => "38127",
                                    "countryCode" => "US",
                                    "residential" => false
                                ],
                                "accountNumber" => [
                                    "value" => $this->account_number
                                ]
                            ]
                        ],
                        "paymentType" => "COLLECT"
                    ],
                    "requestedPackageLineItems" => [
                        [
                            "declaredValue" => [
                                "amount" => 12,
                                "currency" => "USD"
                            ],
                            "weight" => [
                                "units" => "LB",
                                "value" => 68.25
                            ],
                            "dimensions" => [
                                "length" => 100,
                                "width" => 50,
                                "height" => 30,
                                "units" => "CM"
                            ],
                            "packageSpecialServices" => [
                                "specialServiceTypes" => [
                                    "DANGEROUS_GOODS",
                                    "COD"
                                ],
                                "codDetail" => [
                                    "codCollectionAmount" => [
                                        "amount" => 12.45,
                                        "currency" => "USD"
                                    ]
                                ],
                                "dryIceWeight" => [
                                    "units" => "LB",
                                    "value" => 10
                                ],
                                "dangerousGoodsDetail" => [
                                    "accessibility" => "ACCESSIBLE",
                                    "options" => [
                                        "BATTERY"
                                    ]
                                ],
                                "alcoholDetail" => [
                                    "alcoholRecipientType" => "LICENSEE",
                                    "shipperAgreementType" => "retailer"
                                ],
                                "pieceCountVerificationBoxCount" => 2,
                                "batteryDetails" => [
                                    "batteryMaterialType" => "LITHIUM_METAL",
                                    "batteryPackingType" => "CONTAINED_IN_EQUIPMENT",
                                    "batteryRegulatoryType" => "IATA_SECTION_II"
                                ]
                            ]
                        ]
                    ],
                    "shipmentSpecialServices" => [
                        "specialServiceTypes" => [
                            "BROKER_SELECT_OPTION"
                        ],
                        "codDetail" => [
                            "codCollectionAmount" => [
                                "amount" => 12.45,
                                "currency" => "USD"
                            ],
                            "codCollectionType" => "PERSONAL_CHECK"
                        ],
                        "internationalControlledExportDetail" => [
                            "type" => "DSP_LICENSE_AGREEMENT"
                        ],
                        "homeDeliveryPremiumDetail" => [
                            "homedeliveryPremiumType" => "EVENING"
                        ],
                        "holdAtLocationDetail" => [
                            "locationId" => "YBZA",
                            "locationType" => "FEDEX_ONSITE",
                            "locationContactAndAddress" => [
                                "contact" => [
                                    "personName" => "John Taylor",
                                    "emailAddress" => "sample@company.com",
                                    "phoneNumber" => "1234567890",
                                    "phoneExtension" => "1234",
                                    "faxNumber" => "1234567890",
                                    "companyName" => "Fedex"
                                ],
                                "address" => [
                                    "city" => "Collierville",
                                    "stateOrProvinceCode" => "TN",
                                    "postalCode" => "38127",
                                    "countryCode" => "US",
                                    "residential" => false
                                ]
                            ]
                        ],
                        "shipmentDryIceDetail" => [
                            "totalWeight" => [
                                "units" => "LB",
                                "value" => 10
                            ],
                            "packageCount" => 12
                        ]
                    ],
                    "customsClearanceDetail" => [
                        "commodities" => [
                            [
                                "description" => "DOCUMENTS",
                                "quantity" => 1,
                                "unitPrice" => [
                                    "amount" => 12.45,
                                    "currency" => "USD"
                                ],
                                "weight" => [
                                    "units" => "LB",
                                    "value" => 10
                                ],
                                "customsValue" => [
                                    "amount" => 12.45,
                                    "currency" => "USD"
                                ],
                                "numberOfPieces" => 1,
                                "countryOfManufacture" => "US",
                                "quantityUnits" => "PCS",
                                "name" => "DOCUMENTS",
                                "harmonizedCode" => "080211",
                                "partNumber" => "P1"
                            ]
                        ]
                    ]
                ],
                "carrierCodes" => [
                    "FDXG"
                ]





            ]),

        ]);
        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getBody(), true);

        if ($statusCode == 200) {
            return response()->json($responseData);
        } else {
            return response();
        }

    }
}

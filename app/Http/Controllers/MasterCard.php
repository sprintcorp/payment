<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use MasterCard\Core\Model\RequestMap;
use MasterCard\Core\ApiConfig;
use MasterCard\Core\Exception\ApiException;
use MasterCard\Core\Security\OAuth\OAuthAuthentication;
use MasterCard\Api\Crossborder\Quotes;

class MasterCard extends Controller
{
    protected $consumerKey = "your consumer key";   // You should copy this from "My Keys" on your project page e.g. UTfbhDCSeNYvJpLL5l028sWL9it739PYh6LU5lZja15xcRpY!fd209e6c579dc9d7be52da93d35ae6b6c167c174690b72fa
    protected $keyAlias = "keyalias";   // For production: change this to the key alias you chose when you created your production key
    protected $keyPassword = "keystorepassword";   // For production: change this to the key alias you chose when you created your production key
    protected $partnerId = "ptnr_BEeCrYJHh";
    protected $privateKey;
    protected $proposal;

    //
    public function __construct()
    {
        $this->privateKey = file_get_contents(getcwd() . "\EdecoPay-sandbox.p12");

        // e.g. /Users/yourname/project/sandbox.p12 | C:\Users\yourname\project\sandbox.p12
        ApiConfig::setAuthentication(new OAuthAuthentication($this->consumerKey, $this->privateKey,
            $this->keyAlias, $this->keyPassword));
        ApiConfig::setDebug(true); // Enable http wire logging
        ApiConfig::setSandbox(true);   // For production: use ApiConfig::setSandbox(false)
    }

    public function testPayment(){
        $this->getProposal();
    }


    public function getProposal()
    {
        try {
            $map = new RequestMap();
            $map->set("partner-id", $this->partnerId);
            $map->set("quoterequest.transaction_reference", "09930432584935301737");
            $map->set("quoterequest.sender_account_uri", "tel:+254060005");
            $map->set("quoterequest.recipient_account_uri", "tel:+254110108");
            //$map->set("quoterequest.bank_code", "NP021");
            $map->set("quoterequest.quote_type.reverse.sender_currency", "USD");
            $map->set("quoterequest.payment_amount.amount", "100.00");
            $map->set("quoterequest.payment_amount.currency", "USD");
            $map->set("quoterequest.payment_origination_country", "USA");
            $map->set("quoterequest.payment_type", "P2P");
            $this->proposal = $response = Quotes::create($map);

            $this->out($response, "quote.transaction_reference"); //-->09005852143124900054
            $this->out($response, "quote.payment_type"); //-->P2P
            $this->out($response, "quote.proposals.proposal[0].id"); //-->pen_40000156212004054764576101
            $this->out($response, "quote.proposals.proposal[0].resource_type"); //-->proposal
            $this->out($response, "quote.proposals.proposal[0].fees_included"); //-->true
            $this->out($response, "quote.proposals.proposal[0].charged_amount.currency"); //-->USD
            $this->out($response, "quote.proposals.proposal[0].charged_amount.amount"); //-->105.00
            $this->out($response, "quote.proposals.proposal[0].credited_amount.currency"); //-->MAD
            $this->out($response, "quote.proposals.proposal[0].credited_amount.amount"); //-->1000.00
            $this->out($response, "quote.proposals.proposal[0].principal_amount.currency"); //-->USD
            $this->out($response, "quote.proposals.proposal[0].principal_amount.amount"); //-->100.00
            $this->out($response, "quote.proposals.proposal[0].expiration_date"); //-->2018-11-19T03:56:30.072-06:00
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.resource_type"); //-->list
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.item_count"); //-->2
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.data.data_field[0].name"); //-->851
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.data.data_field[0].value"); //-->456
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.data.data_field[1].name"); //-->813
            $this->out($response, "quote.proposals.proposal[0].additional_data_list.data.data_field[1].value"); //-->123
            $this->out($response, "quote.proposals.proposal[0].quote_fx_rate"); //-->777
            // This sample shows looping through quote.proposals.proposal
            echo "This sample shows looping through quote.proposals.proposal\n";
            $list = $response->get("quote.proposals.proposal");
            foreach ($list as $item) {
                $this->outObj($item, "id");
                $this->outObj($item, "resource_type");
                $this->outObj($item, "fees_included");
                $this->outObj($item, "charged_amount");
                $this->outObj($item, "credited_amount");
                $this->outObj($item, "principal_amount");
                $this->outObj($item, "expiration_date");
                $this->outObj($item, "additional_data_list");
                $this->outObj($item, "quote_fx_rate");
            }

        } catch (ApiException $e) {
            $this->err("HttpStatus: " . $e->getHttpStatus());
            $this->err("Message: " . $e->getMessage());
            $this->err("ReasonCode: " . $e->getReasonCode());
            $this->err("Source: " . $e->getSource());
            print_r($e);
        }
    }

    function out($response, $key)
    {
        echo "$key-->{$response->get($key)}\n";
    }

    function outObj($response, $key)
    {
        echo "$key-->{$response[$key]}\n";
    }

    function errObj($response, $key)
    {
        echo "$key-->{$response->get($key)}\n";
    }

    function err($message)
    {
        echo "$message \n";
    }

}

<?php

namespace App\Gepg;

use App\Models\Bill;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;

class XmlRequestHelper
{

    public static function GepgSubmissionRequest(Bill $bill)
    {
        $privateKeyPassphrase = config('app.gepg.private_key_passphrase');
        $collectionCenCode = config('app.gepg.collection_cent_code');
        $Gsfcode = config('app.gepg.gfs_code');
        $SubSpCode = config('app.gepg.sub_sp_code');
        //Function to get Data string

        $bill_exp = Carbon::now()->addMonths(10)->format('Y-m-d\TH:i:s');
        $key_file = config('app.gepg.private_key_file');
        if (!$cert_store = file_get_contents($key_file)) {
            Log::error("\n\n --------Error: \n *** Unable to read the cert file\n");
            throw new FileNotFoundException("Unable to read the cert file: $key_file");
        } else {
            $id = $bill->id;
            if (openssl_pkcs12_read($cert_store, $cert_info, $privateKeyPassphrase)) {
                //Bill Request
                $spcode = config('app.gepg.sp_code');
                $systemid = config('app.gepg.system_id');

                $reqID = GeneralCustomHelper::generateReqID(16);
                $genDate = GeneralCustomHelper::getGenerationDate();
                $content = "<billSubReq>
                        <BillHdr>
                            <ReqId>" . $reqID . "</ReqId>
                            <SpGrpCode>" . $spcode . "</SpGrpCode>
                            <SysCode>" . $systemid . "</SysCode>
                            <BillTyp>1</BillTyp>
                            <PayTyp>2</PayTyp>
                            <GrpBillId>" . $id . "</GrpBillId>
                        </BillHdr>
                        <BillDtls>
                            <BillDtl>
                                <BillId>" . $id . "</BillId>
                                <SpCode>" . $spcode . "</SpCode>
                                <CollCentCode>" . $collectionCenCode . "</CollCentCode>
                                <BillDesc>" . htmlspecialchars($bill->description, ENT_XML1, 'UTF-8') . "</BillDesc>
                                <CustTin>111111111</CustTin>
                                <CustId>" . $bill->user_id . "</CustId>
                                <CustIdTyp>5</CustIdTyp>
                                <CustAccnt>" . GeneralCustomHelper::formatThePhoneNumber($bill->phone_number) . "</CustAccnt>
                                <CustName>" . htmlspecialchars($bill->customer_name, ENT_XML1, 'UTF-8') . "</CustName>
                                <CustCellNum>" . GeneralCustomHelper::formatThePhoneNumber($bill->phone_number) . "</CustCellNum>
                                <CustEmail>" . $bill->customer_email . "</CustEmail>
                                <BillGenDt>" . $genDate . "</BillGenDt>
                                <BillExprDt>" . $bill_exp . "</BillExprDt>
                                <BillGenBy>" . htmlspecialchars($bill->approved_by, ENT_XML1, 'UTF-8') . "</BillGenBy>
                                <BillApprBy>" . htmlspecialchars($bill->approved_by, ENT_XML1, 'UTF-8') . "</BillApprBy>
                                <BillAmt>" . $bill->amount . "</BillAmt>
                                <BillEqvAmt>" . $bill->amount . "</BillEqvAmt>
                                <MinPayAmt>" . $bill->amount . "</MinPayAmt>
                                <Ccy>" . $bill->ccy . "</Ccy>
                                <ExchRate>1.0</ExchRate>
                                <BillPayOpt>" . $bill->payment_option . "</BillPayOpt>
                                <PayPlan>1</PayPlan>
                                <PayLimTyp>1</PayLimTyp>
                                <PayLimAmt>0.00</PayLimAmt>
                                <CollPsp/>
                                <BillItems>
                                    <BillItem>
                                        <RefBillId>" . $id . "</RefBillId>
                                        <SubSpCode>" . $SubSpCode . "</SubSpCode>
                                        <GfsCode>" . $Gsfcode . "</GfsCode>
                                        <BillItemRef>" . $id . "</BillItemRef>
                                        <UseItemRefOnPay>N</UseItemRefOnPay>
                                        <BillItemAmt>" . $bill->amount . "</BillItemAmt>
                                        <BillItemEqvAmt>" . $bill->amount . "</BillItemEqvAmt>
                                        <CollSp>" . $spcode . "</CollSp>
                                    </BillItem>
                                </BillItems>
                                </BillDtl>
                        </BillDtls>
                    </billSubReq>";

                //create signature
                // Log::info($cert_info['pkey']);
                openssl_sign($content, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                //output crypted data base64 encoded
                $signature = base64_encode($signature);
                //Combine signature and content signed
                $requestUri = config('app.gepg.submission_uri');
                $signedPayload = "<Gepg>" . $content . "<signature>" . $signature . "</signature></Gepg>";
                //Perform Curl to a Gepg
                Log::info("-----START SUBMISSION FOR BILL-----\n");
                Log::info("### BILL ID:", ['Bill ID' => $id]);

                Log::info("###  BILL SUB REQ ID:", ['Sub Req ID' => $reqID]);
                // Log::info("###  BILL SUB REQ:",['Sub Req'=> $content]);
                $resultCurlPost = GeneralCustomHelper::performCurlSignedPayload($signedPayload, $requestUri);
                if (!empty($resultCurlPost)) {
                    Log::info("### BILL SUB RES CODE:", ['Code' => GeneralCustomHelper::get_string_between($resultCurlPost, '<AckStsCode>', '</AckStsCode>')]);
                    Log::info("### BILL SUB RES DESC:", ['Description' => GeneralCustomHelper::get_string_between($resultCurlPost, '<AckStsDesc>', '</AckStsDesc>')]);
                    Log::info("----END SUBMISSION RESPONSE----");

                    $vdata = GeneralCustomHelper::get_string_between($resultCurlPost, '<Gepg>', '<signature>');
                    $vsignature = GeneralCustomHelper::get_string_between($resultCurlPost, '<signature>', '</signature>');

                    //Sign return contents
                    return GeneralCustomHelper::isVerifyPayload($vdata, $vsignature);

                } else {
                    Log::info("No result Returned\n");
                    throw new Exception("No result Returned from $requestUri with payload: $signedPayload");
                }
            } else {
                Log::error("\n\n------Error: Unable to read the cert store.\n");
                throw new Exception("Unable to read cert store: '$key_file' with passphrase: '$privateKeyPassphrase'");
            }
        }

    }

    public static function GepgReconciliationRequest($reconsileDate)
    {
        $privateKeyPassphrase = config('app.gepg.private_key_passphrase');
        $privateKeyFile = config('app.gepg.private_key_file');

        //Function to get Data string
        if (!$cert_store = file_get_contents($privateKeyFile)) {
            Log::error("\n\n --------Error: \n *** Unable to read the cert file\n");
            throw new FileNotFoundException("Unable to read the cert file: $privateKeyFile");
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, $privateKeyPassphrase)) {
                //Bill Request
                $systemid = config('app.gepg.system_id');
                $SpGrpCode = config('app.gepg.sp_grp_code');
                $reqID = GeneralCustomHelper::generateReqID(16);

                $content = "<sucSpPmtReq>
                                <ReqId>" . $reqID . "</ReqId>
                                <SpGrpCode>" . $SpGrpCode . "</SpGrpCode>
                                <SysCode>" . $systemid . "</SysCode>
                                <TrxDt>" . $reconsileDate . "</TrxDt>
                                <Rsv1/>
                                <Rsv2/>
                                <Rsv3/>
                            </sucSpPmtReq>";
                //create signature
                openssl_sign($content, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                //output crypted data base64 encoded
                $signature = base64_encode($signature);
                //Combine signature and content signed
                $requestUri = config('app.gepg.reconciliation_uri');
                $signedPayload = "<Gepg>" . $content . "<signature>" . $signature . "</signature></Gepg>";
                //Perform Curl to a Gepg

                Log::info("----- START RECONCILIATION REQUEST -----");
                $resultCurlPost = GeneralCustomHelper::performCurlSignedPayload($signedPayload, $requestUri);
                Log::info("### RECON REQ ID:", ['Request ID' => $reqID]);

                if (!empty($resultCurlPost)) {
                    Log::info(
                        "### ACK RECON CODE:",
                        ['Recon Ack Code' => GeneralCustomHelper::get_string_between($resultCurlPost, '<AckStsCode>', '</AckStsCode>')]
                    );
                    Log::info(
                        "### ACK  RECON DESC:",
                        ['Recon Ack Desc' => GeneralCustomHelper::get_string_between($resultCurlPost, '<AckStsDesc>', '</AckStsDesc>')]
                    );
                    // Log::info("----- ACK:###",[$resultCurlPost]);
                    $vdata = GeneralCustomHelper::get_string_between($resultCurlPost, '<Gepg>', '<signature>');
                    $vsignature = GeneralCustomHelper::get_string_between($resultCurlPost, '<signature>', '</signature>');
                    //Verify Signed Data
                    Log::info("----- END RECONCILLIATION REQUEST  -------\n");
                    return GeneralCustomHelper::isVerifyPayload($vdata, $vsignature);
                } else {
                    Log::info("No result Returned" . "\n");
                    throw new Exception("No result Returned from $requestUri with payload: $signedPayload");
                }
            } else {
                Log::error("\n\n------Error: Unable to read the cert store.\n");
                throw new Exception("Unable to read cert store: '$privateKeyFile' with passphrase: '$privateKeyPassphrase'");
            }
        }

    }

    public static function GepgCancellationRequest($billingData, $cancelledBy, $reason = "Customer over bill")
    {
        $privateKeyPassphrase = config('app.gepg.private_key_passphrase');
        $privateKeyFile = config('app.gepg.private_key_file');

        //Function to get Data string
        if (!$cert_store = file_get_contents($privateKeyFile)) {
            Log::error("\n\n --------Error: \n *** Unable to read the cert file\n");
            throw new FileNotFoundException("Unable to read the cert file: $privateKeyFile");
        } else {

            if (openssl_pkcs12_read($cert_store, $cert_info, $privateKeyFile)) {
                //Bill Request
                $systemid = config('app.gepg.system_id');
                $spGrpCode = config('app.gepg.sp_grp_code');
                $reqID = GeneralCustomHelper::generateReqID(16);
                $content = "<billCanclReq>
                                <ReqId>" . $reqID . "</ReqId>
                                <SpGrpCode>" . $spGrpCode . "</SpGrpCode>
                                <SysCode>" . $systemid . "</SysCode>
                                <BillTyp>1</BillTyp>
                                <GrpBillId>" . $billingData->id . "</GrpBillId>
                                <CanclGenBy>" . $cancelledBy->firstName . " " . $cancelledBy->lastName . "</CanclGenBy>
                                <CanclApprBy>" . $cancelledBy->firstName . " " . $cancelledBy->lastName . "</CanclApprBy>
                                <CanclReasn>" . $reason . "</CanclReasn>
                            </billCanclReq>";

                //create signature
                openssl_sign($content, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                //output crypted data base64 encoded
                $signature = base64_encode($signature);
                //Combine signature and content signed
                $requestUri = config('app.gepg.cancellation_uri');
                $signedPayload = "<Gepg>" . $content . "<signature>" . $signature . "</signature></Gepg>";
                //Perform Curl to a Gepg
                Log::info("--------- GEPG CANCELLATION START -------");
                $resultCurlPost = GeneralCustomHelper::performCurlSignedPayload($signedPayload, $requestUri);

                if (!empty($resultCurlPost)) {
                    Log::info("### CANC REQ ID::", ["ID req" => $reqID]);
                    Log::info("### CANC CODE::", ['Cance ack code' => GeneralCustomHelper::get_string_between($resultCurlPost, '<CanclStsCode>', '</CanclStsCode>')]);
                    Log::info("### CANC DESC::", ['Cance ack desc' => GeneralCustomHelper::get_string_between($resultCurlPost, '<CanclStsDesc>', '</CanclStsDesc>')]);
                    $vdata = GeneralCustomHelper::get_string_between($resultCurlPost, '<Gepg>', '<signature>');
                    $vsignature = GeneralCustomHelper::get_string_between($resultCurlPost, '<signature>', '</signature>');

                    //Verify Data using Certifites
                    Log::info("--------- GEPG CANCELLATION END -------");
                    return GeneralCustomHelper::isVerifyPayload($vdata, $vsignature);
                } else {
                    Log::info("No result Returned" . "\n");
                    throw new Exception("No result Returned from $requestUri with payload: $signedPayload");
                }
            } else {
                Log::error("\n\n------Error: Unable to read the cert store.\n");
                throw new Exception("Unable to read cert store: '$privateKeyFile' with passphrase: '$privateKeyPassphrase'");
            }
        }

    }


}

<?php

namespace App\Gepg;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class GeneralCustomHelper
{
    // global variables

    //random bill ID
    public static function generateReqID($length = 16)
    {
        $randomNumberString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumberString .= mt_rand(0, 9);
        }
        return $randomNumberString;
    }
    // Get date from number of month
    public static function getGenerationDate($lastUntilNoMonth = 0)
    {
        $date = Carbon::now();
        $formattedDate = $date->addMonths($lastUntilNoMonth)->format('Y-m-d\TH:i:s');
        return $formattedDate;
    }
    // Get data between provided xml tag
    public static function get_string_between($xml_string, $tag_start, $tag_end)
    {
        $xml_string = ' ' . $xml_string;
        $ini = strpos($xml_string, $tag_start);
        if ($ini == 0)
            return '';
        $ini += strlen($tag_start);
        $len = strpos($xml_string, $tag_end, $ini) - $ini;
        return substr($xml_string, $ini, $len);
    }
    // Check if the string starts with '+225'
    public static function formatThePhoneNumber($phoneNumber)
    {
        if (substr($phoneNumber, 0, 4) === '+225') {
            // Remove the '+' sign and return the rest of the string
            return '225' . substr($phoneNumber, 4);
        }
        // Return null if the string does not start with '+225'
        return 255738171742;
    }

    public static function isVerifyPayload($vdata, $vsignature)
    {
        $gepgPublicKeyPass = config('app.gepg.public_key_passphrase');
        $gepgPublicCertificate = config('app.gepg.public_key_file');
        //Get Certificate contents
        if (!$pcert_store = file_get_contents($gepgPublicCertificate)) {
            throw new Exception("---Error: Unable to read the cert file\n");
        } else {
            //Read Certificate
            if (openssl_pkcs12_read($pcert_store, $pcert_info, $gepgPublicKeyPass)) {
                //Decode Received Signature String
                $rawsignature = base64_decode($vsignature);
                $verified_data = '';
                //Verify Signature and state whether signature is okay or not
                Log::info($pcert_info['extracerts']);
                Log::info($vdata);
                $ok = openssl_verify($vdata, $rawsignature, $pcert_info['extracerts']['0'], 'sha256WithRSAEncryption');
                if ($ok == 1) {
                    Log::info("------Signature Status: GOOD");
                    $verified_data = $vdata;
                } elseif ($ok == 0) {
                    Log::info("---- Signature Status: BAD");
                    $verified_data = [];
                    throw new Exception("Signature Status: BAD");
                } else {
                    Log::info("Signature Status: UGLY, Error checking signature:");
                    throw new Exception("Signature Status: UGLY, Error checking signature");
                }
                Log::info("---- End Verification ---");
                return $verified_data;
            }
        }
    }

    // perform curl to GEPG
    public static function performCurlSignedPayload($content, $requestUri)
    {
        $GepgBaseUrl = config('app.gepg.base_url');
        $resultCurlPost = "";
        $serverIp = $GepgBaseUrl;
        $uri = $requestUri;
        $data_string = $content;
        $spcode = config('app.gepg.sp_code');
        Log::info("====== CURL MESSAGE GEPG ======");
        $ch = curl_init($serverIp . $uri);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/xml',
                'Gepg-Com:default.sp.in',
                'Gepg-Code:' . $spcode,
                'Gepg-Alg:00S2',
                'Content-Length:' . strlen($data_string)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 70);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 70);
        Log::info("### SIZE PAYLOAD:", ['size' => strlen($data_string)]);

        $resultCurlPost = curl_exec($ch);
        curl_close($ch);

        Log::info("### SIZE RESPONSE:", ['size' => strlen($resultCurlPost)]);
        // Log::info("### RESPONSE:", ['response' => $resultCurlPost]);

        Log::info("====== END CURL MESSAGE GEPG ======");

        return $resultCurlPost;
    }

    // Transform Xml to array

    public static function contrNoXmlResToArray($contrNoXmlData)
    {
        $values = array();
        $values['ResId'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<ResId>', '</ResId>');
        $values['ReqId'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<ReqId>', '</ReqId>');
        $values['BillId'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<BillId>', '</BillId>');
        $values['BillStsCode'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<BillStsCode>', '</BillStsCode>');
        $values['CustCntrNum'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<CustCntrNum>', '</CustCntrNum>');
        $values['ResStsCode'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<ResStsCode>', '</ResStsCode>');
        $values['ResStsDesc'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<ResStsDesc>', '</ResStsDesc>');
        $values['BillStsDesc'] = GeneralCustomHelper::get_string_between($contrNoXmlData, '<BillStsDesc>', '</BillStsDesc>');
        return $values;
    }

    public static function paymentXmlToArray($paymentXmlPayload)
    {
        $values = array();
        // $values['ResId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<ResId>', '</ResId>');
        $values['ReqId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<ReqId>', '</ReqId>');
        $values['GrpBillId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<GrpBillId>', '</GrpBillId>');
        $values['SpGrpCode'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<SpGrpCode>', '</SpGrpCode>');
        $values['EntryCnt'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<EntryCnt>', '</EntryCnt>');
        $values['BillId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<BillId>', '</BillId>');
        $values['SpCode'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<SpCode>', '</SpCode>');
        $values['PspCode'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PspCode>', '</PspCode>');
        $values['PspName'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PspName>', '</PspName>');
        $values['CustCntrNum'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<CustCntrNum>', '</CustCntrNum>');
        $values['PayRefId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PayRefId>', '</PayRefId>');
        $values['TrxId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<TrxId>', '</TrxId>');
        $values['PaidAmt'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PaidAmt>', '</PaidAmt>');
        $values['BillPayOpt'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<BillPayOpt>', '</BillPayOpt>');
        $values['BillAmt'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<BillAmt>', '</BillAmt>');
        $values['Ccy'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<Ccy>', '</Ccy>');
        $values['CollAccNum'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<CollAccNum>', '</CollAccNum>');
        $values['TrxDtTm'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<TrxDtTm>', '</TrxDtTm>');
        $values['UsdPayChnl'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<UsdPayChnl>', '</UsdPayChnl>');
        $values['TrdPtyTrxId'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<TrdPtyTrxId>', '</TrdPtyTrxId>');
        $values['PyrCellNum'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PyrCellNum>', '</PyrCellNum>');
        $values['PyrName'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PyrName>', '</PyrName>');
        $values['PyrEmail'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<PyrEmail>', '</PyrEmail>');
        $values['Rsv1'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<Rsv1>', '</Rsv1>');
        $values['Rsv2'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<Rsv2>', '</Rsv2>');
        $values['Rsv3'] = GeneralCustomHelper::get_string_between($paymentXmlPayload, '<Rsv3>', '</Rsv3>');
        return $values;
    }

    public static function reconcileResXmlToArray($reconcileXmlPayload)
    {
        $values = array();

        // Extracting elements from the XML payload
        $values['ResId'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<ResId>', '</ResId>');
        $values['ReqId'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<ReqId>', '</ReqId>');
        $values['PayStsCode'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<PayStsCode>', '</PayStsCode>');
        $values['PayStsDesc'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<PayStsDesc>', '</PayStsDesc>');
        $values['SpGrpCode'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<SpGrpCode>', '</SpGrpCode>');
        $values['SysCode'] = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<SysCode>', '</SysCode>');

        // Extract the PmtDtls section
        $pmtDtls = GeneralCustomHelper::get_string_between($reconcileXmlPayload, '<PmtDtls>', '</PmtDtls>');

        // Initialize an array to store transaction details
        $pmtTrxDtls = [];

        if ($pmtDtls) {
            // Load the PmtDtls section into a SimpleXMLElement object
            $pmtDtlsXml = simplexml_load_string("<PmtDtls>$pmtDtls</PmtDtls>");

            // Check if the XML is valid and has PmtTrxDtl nodes
            if ($pmtDtlsXml && $pmtDtlsXml->PmtTrxDtl) {
                // Iterate over each PmtTrxDtl node
                foreach ($pmtDtlsXml->PmtTrxDtl as $pmtTrxDtl) {
                    $trxDetail = [];

                    // Extract each field from the PmtTrxDtl node
                    $trxDetail['CustCntrNum'] = (string) $pmtTrxDtl->CustCntrNum;
                    $trxDetail['GrpBillId'] = (string) $pmtTrxDtl->GrpBillId;
                    $trxDetail['SpCode'] = (string) $pmtTrxDtl->SpCode;
                    $trxDetail['BillId'] = (string) $pmtTrxDtl->BillId;
                    $trxDetail['BillCtrNum'] = (string) $pmtTrxDtl->BillCtrNum;
                    $trxDetail['PspCode'] = (string) $pmtTrxDtl->PspCode;
                    $trxDetail['PspName'] = (string) $pmtTrxDtl->PspName;
                    $trxDetail['TrxId'] = (string) $pmtTrxDtl->TrxId;
                    $trxDetail['PayRefId'] = (string) $pmtTrxDtl->PayRefId;
                    $trxDetail['BillAmt'] = (string) $pmtTrxDtl->BillAmt;
                    $trxDetail['PaidAmt'] = (string) $pmtTrxDtl->PaidAmt;
                    $trxDetail['BillPayOpt'] = (string) $pmtTrxDtl->BillPayOpt;
                    $trxDetail['Ccy'] = (string) $pmtTrxDtl->Ccy;
                    $trxDetail['CollAccNum'] = (string) $pmtTrxDtl->CollAccNum;
                    $trxDetail['TrxDtTm'] = (string) $pmtTrxDtl->TrxDtTm;
                    $trxDetail['UsdPayChnl'] = (string) $pmtTrxDtl->UsdPayChnl;
                    $trxDetail['TrdPtyTrxId'] = (string) $pmtTrxDtl->TrdPtyTrxId;
                    $trxDetail['QtRefId'] = (string) $pmtTrxDtl->QtRefId;
                    $trxDetail['PyrCellNum'] = (string) $pmtTrxDtl->PyrCellNum;
                    $trxDetail['PyrEmail'] = (string) $pmtTrxDtl->PyrEmail;
                    $trxDetail['PyrName'] = (string) $pmtTrxDtl->PyrName;
                    $trxDetail['Rsv1'] = (string) $pmtTrxDtl->Rsv1;
                    $trxDetail['Rsv2'] = (string) $pmtTrxDtl->Rsv2;
                    $trxDetail['Rsv3'] = (string) $pmtTrxDtl->Rsv3;

                    // Append the transaction detail to the array
                    $pmtTrxDtls[] = $trxDetail;

                }
            }
        }

        // Return the array of PmtTrxDtl details
        $result = [
            'headers' => $values,
            "paymentDetail" => $pmtTrxDtls
        ];
        return $result;
    }

    public static function signedBillAck($resId, $statusCode)
    {
        $PRIVATE_KEY = config('app.gepg.private_key_file');
        $KEY_PASSWORD = config('app.gepg.private_key_passphrase');

        if (!$cert_store = file_get_contents($PRIVATE_KEY)) {
            Log::info("Error: Unable to read the cert file\n");
            return;
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, $KEY_PASSWORD)) {
                //Response Content Ack
                $AckId = GeneralCustomHelper::generateReqID(16);

                $responseContentAck = "<billSubResAck><AckId>" . $AckId . "</AckId><ResId>" . $resId . "</ResId><AckStsCode>" . $statusCode . "</AckStsCode></billSubResAck>";
                //Create signature
                openssl_sign($responseContentAck, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                // output crypted data base64 encoded
                $signature = base64_encode($signature);
                // Log::info("Signature of Signed Content"."\n".$signature."\n");

                // Combine signature and content signed
                $response = "<Gepg>" . $responseContentAck . " <signature>" . $signature . "</signature></Gepg>";
                // Log::info('------,', [$response]);
                return $response;

            }
        }
    }

    public static function signedPaymentAck($ReqId, $statusCode)
    {
        $PRIVATE_KEY = config('app.gepg.private_key_file');
        $KEY_PASSWORD = config('app.gepg.private_key_passphrase');

        if (!$cert_store = file_get_contents($PRIVATE_KEY)) {
            Log::info("Error: Unable to read the cert file\n");
            return;
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, $KEY_PASSWORD)) {
                //Response Content Ack
                $AckId = GeneralCustomHelper::generateReqID(16);

                $responseContentAck = "<pmtSpNtfReqAck><AckId>" . $AckId . "</AckId><ReqId>" . $ReqId . "</ReqId><AckStsCode>" . $statusCode . "</AckStsCode></pmtSpNtfReqAck>";
                //Create signature
                openssl_sign($responseContentAck, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                // output crypted data base64 encoded
                $signature = base64_encode($signature);
                // Log::info("Signature of Signed Content"."\n".$signature."\n");

                // Combine signature and content signed
                $response = "<Gepg>" . $responseContentAck . " <signature>" . $signature . "</signature></Gepg>";
                // Log::info('------,', [$response]);
                return $response;

            }
        }
    }

    public static function signedReconcileAck($ResId, $statusCode)
    {
        $PRIVATE_KEY = config('app.gepg.private_key_file');
        $KEY_PASSWORD = config('app.gepg.private_key_passphrase');

        if (!$cert_store = file_get_contents($PRIVATE_KEY)) {
            Log::info("Error: Unable to read the cert file\n");
            return;
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, $KEY_PASSWORD)) {
                //Response Content Ack
                $AckId = GeneralCustomHelper::generateReqID(16);

                $responseContentAck = "<sucSpPmtResAck><AckId>" . $AckId . "</AckId><ResId>" . $ResId . "</ResId><AckStsCode>" . $statusCode . "</AckStsCode></sucSpPmtResAck>";
                //Create signature
                openssl_sign($responseContentAck, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                // output crypted data base64 encoded
                $signature = base64_encode($signature);
                // Log::info("Signature of Signed Content"."\n".$signature."\n");

                // Combine signature and content signed
                $response = "<Gepg>" . $responseContentAck . " <signature>" . $signature . "</signature></Gepg>";
                // Log::info('------,', [$response]);
                return $response;

            }
        }
    }

    public static function signedCancellationAck($ResId, $statusCode)
    {
        $PRIVATE_KEY = config('app.gepg.private_key_file');
        $KEY_PASSWORD = config('app.gepg.private_key_passphrase');

        if (!$cert_store = file_get_contents($PRIVATE_KEY)) {
            Log::info("Error: Unable to read the cert file\n");
            return;
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, $KEY_PASSWORD)) {
                //Response Content Ack
                $AckId = GeneralCustomHelper::generateReqID(16);

                $responseContentAck = "<sucSpPmtResAck><AckId>" . $AckId . "</AckId><ResId>" . $ResId . "</ResId><AckStsCode>" . $statusCode . "</AckStsCode></sucSpPmtResAck>";
                //Create signature
                openssl_sign($responseContentAck, $signature, $cert_info['pkey'], "sha256WithRSAEncryption");
                // output crypted data base64 encoded
                $signature = base64_encode($signature);
                Log::info("Signature of Signed Content" . "\n" . $signature . "\n");

                // Combine signature and content signed
                $response = "<Gepg>" . $responseContentAck . " <signature>" . $signature . "</signature></Gepg>";
                // Log::info('------,', [$response]);
                return $response;

            }
        }
    }
}

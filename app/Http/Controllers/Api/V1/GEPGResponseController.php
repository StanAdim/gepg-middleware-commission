<?php

namespace App\Http\Controllers\Api\V1;

use App\Gepg\XmlResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class GEPGResponseController extends Controller
{

    private static function processXmlWrapper(Request $request, $handler)
    {
        // Ensure the content type is application/xml
        if ($request->header('Content-Type') !== 'application/xml') {
            return;
        }

        try {
            // Get raw XML data from the request
            $xmlContent = $request->getContent();
            return XmlResponseHelper::$handler($xmlContent);

        } catch (\Exception $e) {
            // Log the error and return a response
            Log::error('Error processing XML: ' . $e->getMessage());
        }
    }

    public function controlNoResponse(Request $request)
    {
        return self::processXmlWrapper($request, 'handleContrlNoResponse');
    }

    public function paymentReceipt(Request $request)
    {
        return self::processXmlWrapper($request, 'handlePaymentReceipt');
    }

    public function reconcileReceipt(Request $request)
    {
        return self::processXmlWrapper($request, 'handleReconcileReceipt');
    }
}

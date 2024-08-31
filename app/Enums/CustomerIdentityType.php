<?php

namespace App\Enums;

enum CustomerIdentityType: int
{
    case NationalIdentificationNumber = 1;
    case DriverLicense = 2;
    case TaxPayerIdentification = 3;
    case WalletPayNumber = 4;
}

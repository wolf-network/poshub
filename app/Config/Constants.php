<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

/* Company Name */
defined('APP_NAME') OR define('APP_NAME', 'POS Hub');
defined('COMPANY_NAME') OR define('COMPANY_NAME', 'Wolf Network (OPC) PVT LTD');
defined('COMP_ADDRESS') OR define('COMP_ADDRESS', 'House No. 35, Ussap, Latambarcem, next to nanora bus stop, Bicholim, Goa 403503');
defined('COMPANY_EMAIL') OR define('COMPANY_EMAIL', 'info@wolfnetwork.in');
defined('COMPANY_CONTACT_NO') OR define('COMPANY_CONTACT_NO', '+91 9137166653');
defined('GSTNO') OR define('GSTNO', '');
defined('RECAPTCHA_SECRET_KEY') OR define('RECAPTCHA_SECRET_KEY', '');
defined('RECAPTCHA_SITE_KEY') OR define('RECAPTCHA_SITE_KEY', '');

/* Define tables in constant */
defined('REGISTERED_USERS') OR define('REGISTERED_USERS', 'registered_users');
defined('CLIENTS') OR define('CLIENTS', 'clients');
defined('BRANDS') OR define('BRANDS', 'brands');
defined('STATUS') OR define('STATUS', ['Active', 'Inactive', 'Blocked']);
defined('VENDORS') OR define('VENDORS', 'vendors');
defined('SERVICE_MASTER') OR define('SERVICE_MASTER', 'service_master');
defined('VENDOR_SERVICE_MAPPER') OR define('VENDOR_SERVICE_MAPPER', 'vendor_service_mapper');
defined('COUNTRIES_MASTER') OR define('COUNTRIES_MASTER', 'countries_master');
defined('STATES_MASTER') OR define('STATES_MASTER', 'states_master');
defined('CITIES_MASTER') OR define('CITIES_MASTER', 'cities_master');
defined('VENDOR_DOCUMENTS') OR define('VENDOR_DOCUMENTS', 'vendor_documents');
defined('VENDOR_DOCUMENT_MEDIA') OR define('VENDOR_DOCUMENT_MEDIA', 'vendor_document_media');
defined('VENDOR_USERS') OR define('VENDOR_USERS', 'vendor_users');
defined('VENDOR_USER_GEOGRAPHIC_DATA') OR define('VENDOR_USER_GEOGRAPHIC_DATA', 'vendor_user_geographic_data');
defined('FIRM_TYPE_MASTER') OR define('FIRM_TYPE_MASTER', 'firm_type_master');
defined('ROLES_MASTER') OR define('ROLES_MASTER', 'roles_master');
defined('VENDOR_USER_ROLES_MAPPER') OR define('VENDOR_USER_ROLES_MAPPER', 'vendor_user_roles_mapper');
defined('BANKS_MASTER') OR define('BANKS_MASTER', 'banks_master');
defined('BANK_DETAILS') OR define('BANK_DETAILS', 'bank_details');
defined('VENDOR_GEOGRAPHY') OR define('VENDOR_GEOGRAPHY', 'vendor_geography');
defined('VENDOR_BANKING_DOCUMENTS') OR define('VENDOR_BANKING_DOCUMENTS', 'vendor_banking_documents');
defined('BUSINESS_INDUSTRY_MASTER') OR define('BUSINESS_INDUSTRY_MASTER', 'business_industry_master');
defined('CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA') OR define('CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA', 'client_business_industry_mapper_data');
defined('CLIENT_DOCUMENTS') OR define('CLIENT_DOCUMENTS', 'client_documents');
defined('CLIENT_USERS') OR define('CLIENT_USERS', 'client_users');
defined('CLIENT_USER_ROLES_MAPPER') OR define('CLIENT_USER_ROLES_MAPPER', 'client_user_roles_mapper');
defined('CLIENT_GEOGRAPHY') OR define('CLIENT_GEOGRAPHY', 'client_geography');
defined('CLIENT_DOCUMENT_MEDIA') OR define('CLIENT_DOCUMENT_MEDIA', 'client_document_media');
defined('CLIENT_BANKING_DOCUMENTS') OR define('CLIENT_BANKING_DOCUMENTS', 'client_banking_documents');
defined('CLIENT_USER_GEOGRAPHIC_DATA') OR define('CLIENT_USER_GEOGRAPHIC_DATA', 'client_user_geographic_data');
defined('REGISTERED_USER_ROLE_MAPPER') OR define('REGISTERED_USER_ROLE_MAPPER', 'registered_user_role_mapper');
defined('APPS') OR define('APPS', 'apps');
defined('REGISTERED_USER_APP_MAPPER') OR define('REGISTERED_USER_APP_MAPPER', 'registered_user_app_mapper');
defined('PRIVILEGES_MASTER') OR define('PRIVILEGES_MASTER', 'privileges_master');
defined('STATUS_MASTER') OR define('STATUS_MASTER', 'status_master');
defined('REGISTERED_USER_SUBSCRIPTION_LOGS') OR define('REGISTERED_USER_SUBSCRIPTION_LOGS', 'registered_user_subscription_logs');
defined('SUBSCRIPTION_PLANS') OR define('SUBSCRIPTION_PLANS', 'subscription_plans');
defined('COMPANY_DOCUMENTS') OR define('COMPANY_DOCUMENTS', 'company_documents');
defined('COMPANY_MASTER') OR define('COMPANY_MASTER', 'company_master');
defined('COMPANY_ADDRESS') OR define('COMPANY_ADDRESS', 'company_address');
defined('SOCIAL_PLATFORMS') OR define('SOCIAL_PLATFORMS', 'social_platforms');
defined('COMPANY_SOCIAL_MEDIA') OR define('COMPANY_SOCIAL_MEDIA', 'company_social_media');
defined('PRIVILEGES_MASTER') OR define('PRIVILEGES_MASTER', 'privileges_master');
defined('COMPANY_BANKING_DETAILS') OR define('COMPANY_BANKING_DETAILS', 'company_banking_details');
defined('REMINDER') OR define('REMINDER', 'reminder');


/* Finance table Constants */
defined('INVOICE') OR define('INVOICE', 'invoice');
defined('INVOICE_DETAILS') OR define('INVOICE_DETAILS', 'invoice_details');
defined('INVOICE_DETAILS_TAX_DATA') OR define('INVOICE_DETAILS_TAX_DATA', 'invoice_details_tax_data');
defined('TRANSACTIONS') OR define('TRANSACTIONS', 'transactions');
defined('INVOICE_SETTINGS') OR define('INVOICE_SETTINGS', 'invoice_settings');
defined('SERVICE_TAX_TYPES_MASTER') OR define('SERVICE_TAX_TYPES_MASTER', 'service_tax_types_master');
defined('RECEIPTS') OR define('RECEIPTS', 'receipts');
defined('PAYMENT_MODE_MASTER') OR define('PAYMENT_MODE_MASTER', 'payment_mode_master');
defined('INVOICE_DEDUCTIBLES') OR define('INVOICE_DEDUCTIBLES', 'invoice_deductibles');
defined('INVOICE_ADDITIONAL_CHARGES') OR define('INVOICE_ADDITIONAL_CHARGES', 'invoice_additional_charges');
defined('EXPENSE_HEADS_MASTER') OR define('EXPENSE_HEADS_MASTER', 'expense_heads_master');
defined('EXPENSES') OR define('EXPENSES', 'expenses');
defined('COMPANY_SERVICE_TAX_MASTER') OR define('COMPANY_SERVICE_TAX_MASTER', 'company_service_tax_master');
defined('TAX_IDENTIFICATION_TYPE_MASTER') OR define('TAX_IDENTIFICATION_TYPE_MASTER', 'tax_identification_type_master');
defined('CLIENT_SERVICE_TAX_TYPE_MAPPER') OR define('CLIENT_SERVICE_TAX_TYPE_MAPPER', 'client_service_tax_type_mapper');
defined('VENDOR_SERVICE_TAX_TYPE_MAPPER') OR define('VENDOR_SERVICE_TAX_TYPE_MAPPER', 'vendor_service_tax_type_mapper');
defined('PURCHASE_ORDER') OR define('PURCHASE_ORDER', 'purchase_order');
defined('PURCHASE_ORDER_DETAILS') OR define('PURCHASE_ORDER_DETAILS', 'purchase_order_details');
defined('PURCHASE_ORDER_STATUS_MASTER') OR define('PURCHASE_ORDER_STATUS_MASTER', 'purchase_order_status_master');
defined('PURCHASE_ORDER_SETTINGS') OR define('PURCHASE_ORDER_SETTINGS', 'purchase_order_settings');
defined('CREDIT_NOTES') OR define('CREDIT_NOTES', 'credit_notes');
defined('CREDIT_NOTE_DETAILS') OR define('CREDIT_NOTE_DETAILS', 'credit_note_details');
defined('CREDIT_NOTE_DETAILS_TAX_DATA') OR define('CREDIT_NOTE_DETAILS_TAX_DATA', 'credit_note_details_tax_data');
defined('REGISTERED_USER_BANK_ACCOUNT_DETAILS') OR define('REGISTERED_USER_BANK_ACCOUNT_DETAILS', 'registered_user_bank_account_details');
defined('EXPENSE_TAXES') OR define('EXPENSE_TAXES', 'expense_taxes');
defined('DEBIT_NOTES') OR define('DEBIT_NOTES', ' debit_notes');
defined('DEBIT_NOTE_DETAILS') OR define('DEBIT_NOTE_DETAILS', ' debit_note_details');
defined('DEBIT_NOTE_DETAILS_TAX_DATA') OR define('DEBIT_NOTE_DETAILS_TAX_DATA', ' debit_note_details_tax_data');

/* Inventory Table Constants */
defined('ITEMS') OR define('ITEMS', 'items');
defined('ITEM_TAXES') OR define('ITEM_TAXES', 'item_taxes');
defined('STOCK_INWARD_LOGS') OR define('STOCK_INWARD_LOGS', 'stock_inward_logs');
defined('INWARD_OUTWARD_REPORTS') OR define('INWARD_OUTWARD_REPORTS', 'inward_outward_reports');
defined('ITEM_CATEGORY_MASTER') OR define('ITEM_CATEGORY_MASTER', 'item_category_master');
defined('RETURNED_EXPIRING_STOCKS') OR define('RETURNED_EXPIRING_STOCKS', 'returned_expiring_stocks');
defined('STOCK_INWARD_TAXES') OR define('STOCK_INWARD_TAXES', 'stock_inward_taxes');
<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// $routes->get('/', 'Home::index');

/* Frontend routes start */
$routes->get('/', '\Modules\Layouts\Controllers\Pilot::index');
$routes->add('login', '\Modules\Layouts\Controllers\Pilot::login',['get','post']);
$routes->get('logout', '\Modules\Layouts\Controllers\Pilot::logout');
$routes->add('forgot-password', '\Modules\Layouts\Controllers\Pilot::forgotPassword',['get','post']);
$routes->add('register', '\Modules\Layouts\Controllers\Pilot::register',['get','post']);
/* Frontend routes end */

/* Registered users module routes start */
$routes->get('dashboard', '\Modules\Registered_users\Controllers\Registered_user_controller::dashboard');
$routes->get('inventory-overview', '\Modules\Registered_users\Controllers\Registered_user_controller::inventoryOverviewDashboard');
$routes->add('add-user', '\Modules\Registered_users\Controllers\Registered_user_controller::saveUser',['get','post']);
$routes->add('edit-user/(:num)', '\Modules\Registered_users\Controllers\Registered_user_controller::saveUser/$1',['get','post']);
$routes->add('manage-users', '\Modules\Registered_users\Controllers\Registered_user_controller::manageUsers',['get','post']);
$routes->get('change-user-status/(:num)', '\Modules\Registered_users\Controllers\Registered_user_controller::changeUserStatus/$1');
$routes->get('view-referral-details', '\Modules\Registered_users\Controllers\Registered_user_controller::viewReferralDetails');
$routes->add('save-registered-user-bank-details', '\Modules\Registered_users\Controllers\Registered_user_controller::saveRegisteredUserBankDetails',['get','post']);
$routes->add('reset-password', '\Modules\Registered_users\Controllers\Registered_user_controller::resetPassword',['get','post']);
$routes->get('manage-reminders', '\Modules\Registered_users\Controllers\Registered_user_controller::manageReminders');
$route['reset-password'] = 'registered_users/registered_user_controller/resetPassword';
/* Registered user module routes End */


/* Client module routes Start */
$routes->add('add-client', '\Modules\Clients\Controllers\Client_controller::saveClient',['get','post']);
$routes->add('edit-client/(:num)', '\Modules\Clients\Controllers\Client_controller::saveClient/$1',['get','post']);
$routes->get('manage-clients', '\Modules\Clients\Controllers\Client_controller::manageClients');
$routes->get('delete-client/(:num)', '\Modules\Clients\Controllers\Client_controller::deleteClient/$1');
$routes->get('manage-client-service-taxes/(:num)', '\Modules\Clients\Controllers\Client_controller::manageClientServiceTaxes/$1');
$routes->add('add-client-service-tax/(:num)', '\Modules\Clients\Controllers\Client_controller::saveClientServiceTax/$1');
$routes->add('edit-client-service-tax/(:num)/(:num)', '\Modules\Clients\Controllers\Client_controller::saveClientServiceTax/$1/$2');
$routes->add('delete-client-service-tax/(:num)', '\Modules\Clients\Controllers\Client_controller::deleteClientServiceTax/$1');
$routes->add('export-clients', '\Modules\Clients\Controllers\Client_controller::exportClients');
/* Client module routes End */

/* Vendor module routes Start */
$routes->add('add-vendor', '\Modules\Vendors\Controllers\Vendor_controller::saveVendor',['get','post']);
$routes->add('edit-vendor/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::saveVendor/$1',['get','post']);
$routes->get('manage-vendors', '\Modules\Vendors\Controllers\Vendor_controller::manageVendors');
$routes->get('manage-vendor-documents/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::manageVendorDocuments/$1');
$routes->add('add-vendor-document/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::saveVendorDocument/$1',['get','post']);
$routes->get('manage-vendor-service-taxes/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::manageVendorServiceTax/$1');
$routes->add('add-vendor-service-tax/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::saveVendorServiceTax/$1',['get','post']);
$routes->add('edit-vendor-service-tax/(:num)/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::saveVendorServiceTax/$1/$2',['get','post']);
$routes->add('delete-vendor-service-tax/(:num)', '\Modules\Vendors\Controllers\Vendor_controller::deleteVendorServiceTax/$1',['get','post']);
/* Vendor module routes End */

/* Inventory module routes Start */
$routes->add('add-item', '\Modules\Inventory\Controllers\Item_controller::saveItem',['get','post']);
$routes->add('edit-item/(:num)', '\Modules\Inventory\Controllers\Item_controller::saveItem/$1',['get','post']);
$routes->get('manage-items', '\Modules\Inventory\Controllers\Item_controller::manageItems');
$routes->get('delete-item/(:num)', '\Modules\Inventory\Controllers\Item_controller::deleteItem/$1');
$routes->get('manage-item-categories', '\Modules\Inventory\Controllers\Item_controller::manageItemCategories');
$routes->add('add-item-category', '\Modules\Inventory\Controllers\Item_controller::saveItemCategory',['get','post']);
$routes->add('edit-item-category/(:num)', '\Modules\Inventory\Controllers\Item_controller::saveItemCategory/$1',['get','post']);
$routes->add('add-stock', '\Modules\Inventory\Controllers\Stock_controller::saveStock',['get','post']);
$routes->get('stock-inward-history', '\Modules\Inventory\Controllers\Stock_controller::stockInwardHistory');
$routes->get('stock-outward-history', '\Modules\Inventory\Controllers\Stock_controller::stockOutwardHistory');
$routes->get('stock-inward-outward-report', '\Modules\Inventory\Controllers\Stock_controller::stockInwardOutwardReports');
$routes->get('view-expiring-items', '\Modules\Inventory\Controllers\Stock_controller::viewExpiringItems');
$routes->get('view-returned-expiring-items', '\Modules\Inventory\Controllers\Stock_controller::viewReturnedExpiringItems');
$routes->get('export-expiring-items', '\Modules\Inventory\Controllers\Stock_controller::exportExpiringItems');
/* Inventory module routes End */

/* Finance module routes Start */
$routes->add('pos', '\Modules\Finance\Controllers\Pos_controller::pos',['get','post']);
$routes->add('create-purchase-order', '\Modules\Finance\Controllers\Purchase_order_controller::createPurchaseOrder',['get','post']);
$routes->get('manage-purchase-orders', '\Modules\Finance\Controllers\Purchase_order_controller::managePurchaseOrders');
$routes->get('manage-purchase-order-details/(:num)', '\Modules\Finance\Controllers\Purchase_order_controller::managePurchaseOrderDetails/$1');
$routes->get('download-purchase-order/(:num)', '\Modules\Finance\Controllers\Purchase_order_controller::downloadPurchaseOrder/$1');
$routes->add('edit-purchase-order-settings', '\Modules\Finance\Controllers\Purchase_order_controller::editPurchaseOrderSettings',['get','post']);
$routes->add('create-invoice', '\Modules\Finance\Controllers\Finance_controller::saveInvoice',['get','post']);
$routes->get('manage-invoice-details/(:num)', '\Modules\Finance\Controllers\Finance_controller::manageInvoiceDetails/$1');
$routes->get('download-invoice/(:num)', '\Modules\Finance\Controllers\Finance_controller::downloadInvoice/$1');
$routes->get('manage-invoices', '\Modules\Finance\Controllers\Finance_controller::manageInvoices');
$routes->get('manage-receipts/(:num)', '\Modules\Finance\Controllers\Finance_controller::manageReceipts/$1');
$routes->add('create-receipt/(:num)', '\Modules\Finance\Controllers\Finance_controller::createReceipt/$1',['get','post']);
$routes->get('view-receipt-details/(:num)', '\Modules\Finance\Controllers\Finance_controller::viewReceiptDetails/$1');
$routes->get('export-excel-invoices', '\Modules\Finance\Controllers\Finance_controller::exportExcelInvoices');
$routes->add('invoice-settings', '\Modules\Finance\Controllers\Finance_controller::invoiceSettings',['get','post']);
$routes->add('create-credit-note', '\Modules\Finance\Controllers\Credit_note_controller::saveCreditNote',['get','post']);
$routes->add('manage-credit-notes', '\Modules\Finance\Controllers\Credit_note_controller::manageCreditNotes');
$routes->get('delete-credit-note/(:num)', '\Modules\Finance\Controllers\Credit_note_controller::deleteCreditNote/$1');
$routes->get('export-credit-notes', '\Modules\Finance\Controllers\Credit_note_controller::exportCreditNotes');
$routes->get('manage-credit-note-details/(:num)', '\Modules\Finance\Controllers\Credit_note_controller::manageCreditNoteDetails/$1');
$routes->add('mark-credit-note-paid/(:num)', '\Modules\Finance\Controllers\Credit_note_controller::markCreditNotePaid/$1',['get','post']);
$routes->get('download-credit-note/(:num)', '\Modules\Finance\Controllers\Credit_note_controller::downloadCreditNote/$1');
$routes->add('add-expense', '\Modules\Finance\Controllers\Expense_controller::saveExpense',['get','post']);
$routes->get('view-expenses', '\Modules\Finance\Controllers\Expense_controller::viewExpenses');
$routes->get('delete-expense/(:num)', '\Modules\Finance\Controllers\Expense_controller::deleteExpense/$1');
$routes->get('export-expenses', '\Modules\Finance\Controllers\Expense_controller::exportExcelExpenses');
$routes->add('create-debit-note', '\Modules\Finance\Controllers\Debit_note_controller::saveDebitNote',['get','post']);
$routes->get('manage-debit-notes', '\Modules\Finance\Controllers\Debit_note_controller::manageDebitNotes');
$routes->get('manage-debit-note-details/(:num)', '\Modules\Finance\Controllers\Debit_note_controller::manageDebitNoteDetails/$1');
$routes->get('download-debit-note/(:num)', '\Modules\Finance\Controllers\Debit_note_controller::downloadDebitNote/$1');
$routes->get('delete-debit-note/(:num)', '\Modules\Finance\Controllers\Debit_note_controller::deleteDebitNote/$1');
$routes->get('mark-debit-note-paid/(:num)', '\Modules\Finance\Controllers\Debit_note_controller::markDebitNotePaid/$1');
$routes->get('export-debit-notes', '\Modules\Finance\Controllers\Debit_note_controller::exportDebitNotes');
$routes->get('view-pricing-plans/(:num)', '\Modules\Finance\Controllers\Finance_controller::viewPricingPlans/$1');
$routes->get('user-subscription/(:num)', '\Modules\Finance\Controllers\Finance_controller::userAppSubscription/$1');
$routes->get('plan-renewal', '\Modules\Finance\Controllers\Finance_controller::planRenewal');
$routes->add('user-subscription-response/(:num)', '\Modules\Finance\Controllers\Finance_controller::userAppSubscriptionResponse/$1',['get','post']);
$routes->get('financial-report', '\Modules\Finance\Controllers\Finance_reports_controller::financialReport');
$routes->get('gstr-1', '\Modules\Finance\Controllers\Finance_reports_controller::gstr1');
$routes->get('export-gstr1-excel', '\Modules\Finance\Controllers\Finance_reports_controller::exportGSTR1Excel');
/* Finance module routes End */

/* Company module routes Start */
$routes->add('edit-comp-details', '\Modules\Company\Controllers\Company_controller::editCompany',['get','post']);
$routes->add('manage-company-documents', '\Modules\Company\Controllers\Company_controller::manageCompanyDocuments',['get','post']);
$routes->add('add-company-document', '\Modules\Company\Controllers\Company_controller::saveCompanyDocuments',['get','post']);
$routes->get('delete-company-document/(:num)', '\Modules\Company\Controllers\Company_controller::deleteCompanyDocument/$1');
$routes->add('manage-company-service-taxes', '\Modules\Company\Controllers\Company_controller::manageCompanyServiceTaxes',['get','post']);
$routes->add('add-company-service-tax', '\Modules\Company\Controllers\Company_controller::saveCompanyServiceTax',['get','post']);
$routes->add('edit-company-service-tax/(:num)', '\Modules\Company\Controllers\Company_controller::saveCompanyServiceTax/$1',['get','post']);
$routes->get('delete-company-service-tax/(:num)', '\Modules\Company\Controllers\Company_controller::deleteCompanyServiceTax/$1');
$routes->add('edit-company-bank-details', '\Modules\Company\Controllers\Company_controller::saveCompanyBankDetails',['get','post']);
$routes->get('manage-addresses', '\Modules\Company\Controllers\Company_controller::manageAddresses');
$routes->add('add-address', '\Modules\Company\Controllers\Company_controller::saveAddress',['get','post']);
$routes->add('edit-address/(:num)', '\Modules\Company\Controllers\Company_controller::saveAddress/$1',['get','post']);
$routes->get('delete-address/(:num)', '\Modules\Company\Controllers\Company_controller::deleteAddress/$1');
/* Company module routes End */


/* Management module routes start */

$routes->add('add-role', '\Modules\Management\Controllers\Roles_controller::saveRole',['get','post']);
$routes->add('edit-role/(:num)', '\Modules\Management\Controllers\Roles_controller::saveRole/$1',['get','post']);
$routes->get('manage-roles', '\Modules\Management\Controllers\Roles_controller::manageRoles');
$routes->get('delete-role/(:num)', '\Modules\Management\Controllers\Roles_controller::deleteRole/$1');
$routes->add('add-industry', '\Modules\Management\Controllers\Industries_controller::saveIndustry',['get','post']);
$routes->add('edit-industry/(:num)', '\Modules\Management\Controllers\Industries_controller::saveIndustry/$1',['get','post']);
$routes->get('manage-industries', '\Modules\Management\Controllers\Industries_controller::manageIndustries');
$routes->get('delete-industry/(:num)', '\Modules\Management\Controllers\Industries_controller::deleteIndustry/$1');
$routes->add('add-service', '\Modules\Management\Controllers\Services_controller::saveService',['get','post']);
$routes->add('edit-service/(:num)', '\Modules\Management\Controllers\Services_controller::saveService/$1',['get','post']);
$routes->get('manage-services', '\Modules\Management\Controllers\Services_controller::manageServices');
$routes->get('delete-service/(:num)', '\Modules\Management\Controllers\Services_controller::deleteService/$1');
/* Management module routes end */

/* Api routes start */
$routes->get('api/basic/(:any)', '\Modules\Webservices\Controllers\Basic::$1');
$routes->add('api/finance/(:any)', '\Modules\Webservices\Controllers\Finance::$1',['get','post']);
$routes->add('api/finance_reports/(:any)', '\Modules\Webservices\Controllers\Finance_reports::$1',['get','post']);
$routes->add('api/stock/(:any)', '\Modules\Webservices\Controllers\Stock::$1',['get','post']);
$routes->add('api/registered_users/(:any)', '\Modules\Webservices\Controllers\Registered_users::$1',['get','post']);
$routes->add('api/tasks/(:any)', '\Modules\Webservices\Controllers\Tasks::$1',['get','post']);
$routes->add('api/projects/(:any)', '\Modules\Webservices\Controllers\Projects::$1',['get','post']);
$routes->add('api/clients/(:any)', '\Modules\Webservices\Controllers\Clients::$1',['get','post']);
$routes->add('api/vendors/(:any)', '\Modules\Webservices\Controllers\Vendors::$1',['get','post']);
$routes->add('api/item/(:any)', '\Modules\Webservices\Controllers\Item::$1',['get','post']);
$routes->add('api/company/(:any)', '\Modules\Webservices\Controllers\Company::$1',['get','post']);
$routes->add('api/management/(:any)', '\Modules\Webservices\Controllers\Management::$1',['get','post']);
/* Api routes end */

/* Cron routes start */
$routes->cli('cron/stock-expiry-mail', '\Modules\Cron\Controllers\Cron_controller::stockExpiryReminderMail');
/* Cron routes end */

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
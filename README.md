# Order-Processing-Module
Vendor_CustomOrderProcessing Module for Magento 2

Overview

The Vendor_CustomOrderProcessing module provides a REST API to update order status using authentication. It also logs order status changes and sends an email notification when an order is marked as shipped.

Features

REST API to update order status based on order increment ID.

Event observer (sales_order_save_after) to track order status changes.

Logs status changes in a custom database table (order_status_log).

Sends an email notification when an order is marked as shipped.

Includes Unit and Integration Tests with test coverage verification.

Installation

Navigate to Magento root directory:

cd /path/to/magento/root

Place the module in app/code/Vendor/CustomOrderProcessing/

Run Magento setup commands:

php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush

API Usage

Update Order Status API

Endpoint:

POST /rest/V1/customorderprocessing/update

Request Body:

{
    "increment_id": "100000001",
    "status": "complete"
}

Response:

{
    "message": "Order status updated successfully."
}

Event Observer: Order Status Change

The module listens to the sales_order_save_after event to log order status changes and send an email when an order is marked as shipped.

Logged Data in `` Table:

order_id

old_status

new_status

changed_at (timestamp)

Email Notification:

Triggered when an order is marked as shipped (complete status).

Uses Magento's `` class to send the default order email.

Testing & Coverage

Run Unit Tests:

php vendor/bin/phpunit app/code/Vendor/CustomOrderProcessing/Test/Unit/

Run Integration Tests:

php bin/magento dev:tests:run integration

Generate Test Coverage Report:

cd dev/tests/integration
php ../../../vendor/bin/phpunit --coverage-html ../../../var/coverage/ ../../../app/code/Vendor/CustomOrderProcessing/

Open var/coverage/index.html in a browser to view the report.

License

This module is open-source and can be modified as needed.

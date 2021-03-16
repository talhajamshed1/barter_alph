<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
/*User*/

//Contac us
define("MESSAGE_THANKYOU", 'Thank you for your comment. We will get back to you soon !!');


//Referrels
define("MESSAGE_REFERRAL_ADDED", 'Referrals added successfully!');

define("ERROR_ALREADY_REFERRED", ' already referred. Please enter other referrals');
define("ERROR_ALREADY_IN_SYSTEM", ' already in the system. Please enter other referrals');
define("ERROR_ATLEAST_ONE_REFERRAL_REQUIRED", 'Please make sure you have selected atleast one referral and entered values for fields!');
define("ERROR_REFERRAL_DEACTIVATED", 'Currently the site is under free registration mode hence referral is deactivated!');

define("TEXT_ADD_REFERRALS", 'Add Referrals');
define("TEXT_SURVEY", 'Survey');
define("TEXT_REG", 'Reg');
define("TEXT_DONE", 'Done');
define("TEXT_NOT_DONE", 'Not Done');
define("TEXT_SUCCESSFULL_SURVEYS", 'Successful <b>Surveys</b>');
define("TEXT_SUCCESSFULL_REGISTRATIONS", 'Successful <b>Registrations</b>');
define("TEXT_AMOUNT_PENDING", 'Amount Pending');
define("TEXT_AMOUNT_PAID", 'Amount Paid');
define("TEXT_REFERRAL", 'Referral');


//plan
define("HEADING_CHANGE_PLAN", 'Change Plan');
define("TEXT_PLAN", 'Plan');
define("TEXT_PLANS", 'Plans');
define("TEXT_PAID", 'Paid');
define("TEXT_FREE", 'Free');
define("TEXT_PLAN_NAME", 'Plan Name');
define("TEXT_PAYMENT_METHOD", 'Payment Method');
define("TEXT_TRANSACTION_ID", 'Transaction ID');
define("TEXT_ACTIVE_PLAN", 'Active plan');
define("TEXT_PER_MONTH", 'Per Month');
define("TEXT_PER_YEAR", 'Per Year');

define("ERROR_NO_PLANS_TO_UPGRADE", 'No plans currently available for upgrade');
define("BUTTON_CONTINUE", 'Continue');

define("ERROR_CANT_CANCEL_ACTIVE_PLAN", 'You cannot cancel this subscription plan since the plan is active');
define("MESSAGE_SUBSCRIPTION_CANCELED", 'Subscription canceled successfully');
define("ERROR_SURE_TO_CANCEL_SUBSCRIPTION", 'Are you sure you want to cancel this subscription?');

//Settings
define("MESSAGE_SETTINGS_CHANGED", 'Settings changed successfully!');
define("HEADING_MY_SETTINGS", 'My Settings');
define("TEXT_SUBSCRIBE_NEWSLETTER", 'Subscribe to Newsletters');
define("TEXT_RECEIVE_ALERT_NEW_SALE_ADDITION", 'Receive Alerts on New Sale Addition');

//Feedbacks
define("TEXT_SATISFIED", 'Satisfied');
define("TEXT_DISSATISFIED", 'Dissatisfied');
define("TEXT_NEUTRAL", 'Neutral');
define("HEADING_FEEDBACK_DETAILS", 'Feedback Details');
define("TEXT_RATING", 'Rating');
define("HEADING_SALE_LIST_FROM", 'SALE LIST FROM');

//messages
define("HEADING_RECEIVED_MESSAGES", 'Received Messages');
define("HEADING_SENT_MESSAGES", 'Sent Messages');
define("TEXT_FROM", 'From');
define("TEXT_TO", 'To');
define("MESSAGE_MESSAGE_SENT_SUCCESSFULLY", 'Your message sent successfully');
define("MESSAGE_MESSAGE_DELETED_SUCCESSFULLY", 'Your message deleted successfully');
define("MESSAGE_NO_MORE_MESSAGES", 'No more messages for you');
define("HEADING_READ_MESSAGE", 'Read Message');
define("LINK_REPLY", 'Reply');
define("TEXT_RE", 'Re:');
define("TEXT_ORIGINAL_MESSAGE", 'Original Message');

//Account summary
define("ERROR_MISMATCH_ENTRY", 'Mismatch in the entry');
define("HEADING_SALES_TRANSACTION_DETAILS", 'Sales Transaction Details');
define("HEADING_SWAP_WISH_TRANSACTION_DETAILS", 'Swap/Wish Transaction Details');
define("HEADING_AMOUNT_ACTUALLY_TRANSFERED", 'Amounts actually transfered to your account');
define("HEADING_TRANSACTION_SUMMARY", 'Transaction Summary');
define("TEXT_TRANSACTION_DATE", 'Transaction Date');
define("TEXT_TRANSACTION_MODE", 'Transaction Mode');
define("TEXT_TRANSACTION_NUMBER", 'Transaction Number');
define("TEXT_COMPLETED_TRANSACTIONS", 'Completed Transactions');
define("TEXT_PAYMENTS_RECEIVED", 'Payments Received');
define("TEXT_PAYMENTS_MADE", 'Payments Made');

//forgot pssword
define("TEXT_MAIL_SENT_TO_ENABLE_PASSWORD_RESET", 'A mail has been sent to {email} to enable password reset request. Please follow the instructions on the email');
define("TEXT_FORGOT_PASSWORD", 'Forgot Password');
define("TEXT_ENTER_EMAIL_REGISTRATION", 'Enter your email address provided during registration');
define("BUTTON_RESET_PASSWORD", 'Reset Password');
define("TEXT_PASSWORD_RESET_MAIL_SENT", 'Your password is reset and a mail has been sent you with the new password');
define("ERROR_INVALID_LINK", 'Invalid Link');

//registration 
define("ERROR_USERNAME_EXIST", 'Username already exist');
define("ERROR_EMAIL_EXIST", 'Email already exist');
define("ERROR_USERNAME_INVALID_NO_SPECIAL_CHARS", 'Invalid User Name. Please select a user name with no special characters (@,#,^,& etc. ) and spaces!');
define("ERROR_PASSWORD_SIX_CHAR", 'Password should be atleast six characters long');
define("ERROR_PASSWORD", 'Please enter Password');
define("ERROR_CONFIRM_PASSWORD", 'Please enter Confirm Password');
define("ERROR_PASSWORD_CONFIRM_PASSWORD", 'Password and Confirmation Password should match');
define("ERROR_MISMATCH", 'Mismatch');
define("ERROR_CORRECT", 'Correct');
define("HEADING_REGISTRATION_FORM", 'Registration Form');
define("TEXT_CHOOSE_PLAN", 'Choose Plan');
define("TEXT_CONFIRM_PASSWORD", 'Confirm Password');
define("BUTTON_REGISTER", 'Register');
define("MESSAGE_ACCESS_ACCOUNT_AFTER_ADMIN_APPROVAL", 'Thank you for your registration. Your registration process has been completed, and a mail has been sent to you with registration information.<br>&nbsp;<br> You may log into your account at {site_url} to access our services, after Admin approves your registration');
define("MESSAGE_ACCESS_ACCOUNT_AFTER_EMAIL_VERIFICATION", 'Thank you for your registration. Your registration process has been completed, and a mail has been sent to you with registration information.<br>&nbsp;<br> Click on the link on the email to activate your account. You may log into your account at {site_url} to access our services, after your email activation');
define("MESSAGE_ACCESS_ACCOUNT_NOW", 'Thank you for your registration. Your registration process has been completed, and a mail has been sent to you with registration information.<br>&nbsp;<br> You may log into your account at {site_url} to access our services.');//<br>&nbsp;<br>Please login to access the services of {site_url} - Edited
define("MESSAGE_ACCESS_ACCOUNT",'Thank you for your registration. Please login to your account at {site_url}');
define("MESSAGE_LOGIN_ACCOUNT_AFTER_ADMIN_APPROVAL",'Note: You can login to the site, once the Administrator approves your membership');
define("MESSAGE_LOGIN_ACCOUNT_AFTER_EMAIL_VERIFICATION",'Note: A mail with activation link has been sent to your email. Please click activation link to activate your membership');
define("LINK_CLICK_LOGIN", 'Click here to login');
define("MESSAGE_LOGIN_AT", 'Thank you for your registration. Please login to your account at {site_url}');
define("HEADING_REGISTRATION_INFO", 'Registration Information');
define("TEXT_REGISTRATION_DATE", 'Registration Date');
define("TEXT_MONTH", 'Month');
define("TEXT_USER_REGISTRATION", 'User Registration');

define("HEADING_REGISTRATION_DETAILS", 'Registration Details');
define("TEXT_PAYMENT_REGISTRATION_USER", 'Payment for registration of the user');

//Edit Profile
define("MESSAGE_CHANGES_SAVED", 'Changes saved successfully');
define("MESSAGE_PASSWORD_CHANGED", 'Password changed successfully');
define("ERROR_INVALID_OLD_PASSWORD", 'The old password you entered was incorrect');
define("ERROR_ENTER_PASSWORDS", 'Please enter New Password and Confirm New Password');
define("ERROR_EMPTY_FIRST_NAME", 'First Name cannot be empty');
define("ERROR_EMPTY_LAST_NAME", 'Last Name cannot be empty');
define("ERROR_EMPTY_ADDRESS1", 'Address Line 1 cannot be empty');
define("ERROR_EMPTY_ADDRESS", 'Address cannot be empty');
define("ERROR_EMPTY_CITY", 'City cannot be empty');
define("ERROR_EMPTY_STATE", 'State cannot be empty');
define("ERROR_EMPTY_ZIP", 'Zip cannot be empty');
define("ERROR_ZIP", 'Zip should be numeric and five digit of length');
define("ERROR_EMPTY_COUNTRY", 'Country cannot be empty');
define("ERROR_EMPTY_PHONE", 'Phone cannot be empty');
define("ERROR_EMPTY_CARD_NUMBER", 'Card Number cannot be empty');
define("ERROR_EMPTY_CARD_VERIFICATION_NUMBER", 'Card verification number cannot be empty');
define("ERROR_INVALID_EXPIRY_DATE", 'Invalid expiry date');
define("HEADING_EDIT_PROFILE", 'Edit Profile');
define("TEXT_URL", 'Url');
define("TEXT_GENDER", 'Gender');
define("TEXT_PAYPAL_EMAIL", 'Paypal Email');
define("TEXT_STRIPE_PUBLIC", 'Stripe Public Key');
define("TEXT_STRIPE_SECRET", 'Stripe Public Token');
define("TEXT_PAYPAL_MANDATORY_SETTINGS", 'Mandatory Paypal Settings');
define("HEADING_CHANGE_PASSWORD", 'Change Password');
define("TEXT_OLD_PASSOWRD", 'Old Password');
define("TEXT_NEW_PASSWORD", 'New Password');
define("TEXT_CONFIRM_NEW_PASSWORD", 'Confirm New Password');
define("TEXT_MALE", 'Male');
define("TEXT_FEMALE", 'Female');
define("HEADING_PROFILE_OF", 'Profile of {user_name}');
define("TEXT_NO_SATISFIED_CUSTOMERS", 'Number of Satisfied Customers');
define("TEXT_NO_DISSATISFIED_CUSTOMERS", 'Number of Dissatisfied Customers');
define("TEXT_NO_NEUTRAL_CUSTOMERS", 'Number of Neutral Customers');
define("ERROR_INVALID_IMAGE", 'Image is null or not valid (Only jpg/gif/png are allowed)');
define("ERROR_IMAGE_UPLOADED", 'Image uploaded successfully');
define("HEADING_EDIT_IMAGE", 'Edit Image');
define("TEXT_PROFILE_IMAGE", 'Profile Picture');
define("BUTTON_SAVE_IMAGE", 'Save Image');
define("ERROR_IMAGE_REQUIRED", 'Image needs to be selected');
define("LINK_IMAGE_SETTING", 'Image Setting');
define("TEXT_SHOW_IMAGE_EVERYONE", 'Show Profile Picture to everyone');
define("HEADING_DETAILS_OF_TRANSACTION", 'Details for Transaction');

//Plans
define("ERROR_CANNOT_CANCEL_SUBSCRIPTION", 'You cannot cancel this subscription plan .Please contact ');
define("MESSAGE_SUBSCRIPTION_CANCELLED", 'Subscription cancelled successfully');
define("HEADING_CANCEL_SUBSCRIPTION", 'Cancel Subscription');
define("TEXT_SELECT_PAYMENT_OPTION_FOR_MEMBERSHIP", 'Please select from the payment options below for your membership to ');
define("TEXT_PAMENT_VERIFICATION_RESTRICTIONS", 'Some payment methods requires verification. So only after verification the restrictions would be lifted');
define("HEADING_PLAN_UPGRADATION_COMPLETED", 'Plan Upgradation Process Completed');
define("TEXT_PLAN_UPGRADATION_SUCCESSFULL_TO_CANCEL", 'You have successfully completed plan upgradation - ');
define("TEXT_PAYMENT_FOR_PLAN_UPGRADATION", 'Payment for plan upgradation');


//user forms and data
define("TEXT_COMMENT", 'Comment');
define("TEXT_SECURITYCODE", 'Security Code');


define("ERROR_NAME_EMPTY", 'Name cannot be empty');
define("ERROR_EMAIL_EMPTY", 'Email cannot be empty');
define("ERROR_EMAIL_INVALID", 'Please enter a valid email');
define("ERROR_EMAIL_NOT_UNIQUE", 'Please enter unique email addresses');
define("ERROR_SECURITYCODE_EMPTY", 'Security code can\'t be blank');
define("ERROR_SECURITYCODE_INVALID", 'Invalid security code');

//index
define("HEADING_NEW_ADDITION",'New Additions');
define("HEADING_FEATURED_ITEMS",'Featured Items ');
define("BUTTON_BUYNOW",'Buy Now');
define("MESSAGE_NOFEATURE_AVAILABLE",'Sorry No featured products available');

//tell friend
define("MESSAGE_EMAIL_SENT_THANKYOU_REFERING",'An E-Mail Has Been Sent To all Your Friends. Thank You for Referring This Site');
define("ERROR_YOUR_NAME_EMPTY", 'Your name cannot be empty');
define("ERROR_YOUR_EMAIL_EMPTY", 'Your email cannot be empty');
define("ERROR_YOUR_EMAIL_INVALID", 'Please enter a valid email');
define("HEADING_TELL_FRIEND",'Tell A Friend');
define("TEXT_YOUR_NAME",'Your Name');
define("TEXT_YOUR_EMAIL",'Your Email');
define("TEXT_FOUND_SITE_YOU_LIKE",'I found a website that I thought you\'d like to see');
define("BUTTON_TELL_FRIENDS",'Tell Friends');

//Points
define("TEXT_HISTORY",'History');
define("TEXT_DASHBOARD",'Dashboard');
define("TEXT_AVAILABLE",'Available');
define("MENU_MY_POINTS",'My {point_name}');
define("MENU_SEND_POINTS",'Send {point_name}');
define("MENU_SENT_POINTS",'Sent {point_name}');
define("MENU_RECEIVED_POINTS",'Received {point_name}');
define("MENU_BUY_POINTS",'Buy {point_name}');
define("MENU_PENDING_ORDER_CONFIRMATIONS",'Pending Order Confirmations');
define("MESSAGE_POINT_SENT_SUCCESSFULLY",'{point_name} sent successfully.');
define("ERROR_EMPTY_POINT",'Number of {point_name} to Send can\'t be blank');
define("TEXT_SELECT_USER",'Select User');
define("TEXT_NO_OF_POINTS_TO_SEND",'Number of {point_name} to Send');
define("ERROR_INSUFFICIENT_POINTS",'You are not able to send because your available {point_name} is {points}.');
define("TEXT_SENT_TO",'Sent To');
define("TEXT_SENT_DATE",'Sent Date');
define("TEXT_SENT_BY",'Sent By');
define("HEADING_RECEIVED_POINTS_HISTORY",'Received {point_name} History');
define("HEADING_SENT_POINTS_HISTORY",'Sent {point_name} History');
define("HEADING_SEND_POINT",'Send {point_name}');
define("HEADING_POINTS_PURCHASE_HISTORY",'{point_name} Purchase History');
define("TEXT_PENDING_TO_UPDATE_ACCOUNT",'Pending to update in your account');
define("TEXT_ADDED_TO_ACCOUNT",'Added to your account');
define("HEADING_BUY_POINTS",'Buy {point_name}');
define("TEXT_SELECT_POINT", 'Select {point_name}');
define("TEXT_AMOUNT_TO_PAY",'Amount to Pay');
define("TEXT_PURCHASED_BY", 'Purchased By');
define("TEXT_PRODUCT",'Product');
define("TEXT_PENDING_ADMIN_VERIFICATION", 'Pending admin verification');
define("TEXT_SUCCESS_FEE_FOR_EACH_TRANSACTION", 'Success Fee for Each Successful Transactions');
define("TEXT_SUCCESS_FEE",'Success Fee');
define("TEXT_BUY_POINTS", 'Buy {point_name}');
define("TEXT_SURVEY_STATUS", 'Survey Status');
define("TEXT_SURVEY_COMPLETION_DATE",'Survey Completion Date');
define("TEXT_REGISTRATION_STATUS", 'Registration Status');
define("TEXT_REGISTRATION_COMPLETION_DATE",'Registration Completion Date');
define("MESSAGE_DETAILS_NOT_AVAILABLE", 'Details Not Available');


define("TEXT_ACCOUNT_SUMMARY",'Account Summary');
define("HEADING_TRACK_SHIPMENT",'Track Shipment');
define("CONTENT_SHIPMENT", 'As a member of {site_name}, you may click on one of the carrier service providers below to conveniently manage and track your order status from other members of our community.  To accomplish this, you will need any necessary tracking information to fulfill this request.  Members are strongly encouraged to retrieve this information from partners concluding a sale, or swapping transaction on the site. This can be done by using the tools we provide you on the site such as the members email address or contact number. Once you receive confirmation that your article has been shipped, the member must provide you with the carrier service provider they used and any tracking information relevant for you to track the shipment. Once you obtain this information, you will have the confidence in knowing you did business with a trustworthy partner. At {site_name}, this is the kind of community we are devoted to providing each and every member of our community.');
define("TEXT_USPS",'USPS');
define("TEXT_DHL", 'DHL');
define("TEXT_FEDEX",'FedEx');
define("TEXT_UPS", 'UPS');
define("ERROR_USERNAME_INUSE_SELECT_DIFFERENT",'This User Name is in use! Please select a different one!');
define("ERROR_USERNAME_NOT_ALPHANUMERIC_SELECT_DIFFERENT",'The username contains characters other than letters and numbers. Please provide another username!');
define("ERROR_INVALID_PHONE_NOTALLOWED_ALPHABETS_SP_CHARACTERS", 'Invalid phone number! Alphabets or special characters except - and + are not allowed.');
define("ERROR_TITLE_EMPTY",'Title is empty');
define("MESSAGE_FEEDBACK_POSTED", 'Feedback Posted Successfully');
define("TEXT_FEEDBACK",'Feedback');
define("MESSAGE_SUBSCRIPTION_CANCELLED", 'This subscription has been cancelled');

/*Category*/

define("TEXT_SELECT_CATEGORY",'Select a Category');
define("MESSAGE_SORRY_NO_CATEGORY",'Sorry No categories available');
define("MESSAGE_YOU_SELECTED_CATEGORY",'You\'ve selected a category');
define("LINK_CLICK_CONTINUE",'Click Continue');
define("HEADING_PRODUCTS_PER_CART_DIARY",'Products Per Cart Diary');
define("TEXT_ITEM_LISTINGS",'item listings');
define("OTHER_POSTINGS_CLICK_HERE",'To view other postings click here');
define("MESSAGE_NO_QUANTITY_FOR_PURCHASE",'There is no enough quantity for purchase!');

define("ERROR_INVALID_TYPE",'Given type is not valid');
define("ERROR_FILE_TOO_LARGE",'File Too Large');
define("ERROR_FILE_REQUIRED_FORMAT",'Image should be either of gif or jpg or png formats');
define("MESSAGE_NEW_ITEM_ADDED",'New item added to the {type} list!');
define("MESSAGE_SELECTED_ITEM_UPDATED",'The selected item has been updated');
define("MESSAGE_SELECTED_ITEM_DELETED",'The selected item has been deleted');
define("ERROR_EMPTY_CATEGORY",'Category cannot be empty');
define("ERROR_EMPTY_TITLE",'Title cannot be empty');
define("ERROR_EMPTY_QUANTITY",'Quantity is empty or invalid');
define("ERROR_INVALID_PRICE",'Price cannot be empty');
define("ERROR_SELECT_SUBCATEGORY",'There is a subcategory under the category you have selected. Please select a subcategory!');
define("ERROR_POSITIVE_VALUE",'Please enter a positive numeric value');
define("ERROR_ITEM_ALREADY_SWAPPED_DELETED",'Sorry, this swap item has already been swapped or deleted');
define("ERROR_ITEM_ALREADY_PURCHASED_DELETED",'Sorry, this item has already been purchased or deleted');
define("ERROR_MAX_VALUE_ALLOWED_FOR_POSTING",'The maximum value allowed for this posting is');

define("HEADING_ITEMS",'Items');
define("HEADING_ITEM_DETAILS",'Item Details');
define("HEADING_ITEM_LIST",'Item List');
define("HEADING_ITEM_WISHED",'Item Wished');
define("HEADING_ONLINE_MEMBERS",'Online Members');
define("HEADING_LATEST_SALES_ADDITIONS",'Sale Additions');
define("HEADING_LATEST_SWAP_ADDITIONS",'Swap Additions');
define("HEADING_LATEST_WISH_ADDITIONS",'Wish Additions');
define("HEADING_ULTIMATE_SWAPPING_EXPERIENCE",'The ultimate swapping experience');
define("HEADING_POSTINGS_OF",'Postings of ');
define("HEADING_ADD_NEW_SALE_ITEM",'Add New Sale Item');
define("HEADING_ADD_NEW_SWAP_ITEM",'Add New Swap Item');
define("HEADING_ADD_NEW_WISH_ITEM",'Add New Wish Item');
define("MESSAGE_ITEM_SUCCESSFULLY_LISTED",'Your item has been successfully listed');
define("LINK_EDIT",'Edit');
define("LINK_DETAILS",'Details');

define("TEXT_SWAP_ITEM",'Swap Item');
define("TEXT_WISH_ITEM",'Wish Item');
define("TEXT_SALE_ITEM",'Sale Item');

define("TEXT_SWAP_ITEMS",'Swap Items');
define("TEXT_WISH_ITEMS",'Wish Items');
define("TEXT_SALE_ITEMS",'Sale Items');

define("TEXT_NEW",'New');
define("TEXT_USED",'Used');
define("TEXT_LIKE_NEW",'Like New');
define("TEXT_VERY_GOOD",'Very Good');
define("TEXT_GOOD",'Good');
define("TEXT_IMAGE",'Image');
define("TEXT_SELECT_YEAR",'Select Year');
define("TEXT_PICTURE",'Picture');
define("TEXT_PICTURE_IF_ANY",'Picture (If any)');
define("TEXT_SHIPPING_CHARGE",'Shipping Charge');
define("TEXT_MAX_NO_IMAGES",'You can upload maximum of {max_images} images');
define("TEXT_IMAGE_SIZE_SHOULD_BE",'For best display, image size should be');
define("TEXT_MAX_UPLOAD_SIZE_IS",'Max upload size is {MaxUploadSize} MB');
define("TEXT_ITEM_DESCRIPTION",'Item Description');
define("TEXT_PRICE_FOR_POSTING",'Price for posting');
define("TEXT_LISTING_FEE_RANGE",'Listing Fee Range');
define("TEXT_PRICE_RANGE",'Price Range');
define("TEXT_COMMISSION_FOR_POSTING",'Commission for this posting');
define("LINK_ESCROW_FEES_CALCULATOR",'Escrow Fees Calculator');


define("TEXT_FEATURED",'Featured');
define("TEXT_CATEGORY",'Category');
define("TEXT_BRAND",'Brand');
define("TEXT_CONDITION",'Condition');
define("TEXT_SELLER_NAME",'Seller Name');
define("TEXT_ONLINE",'Online');
define("TEXT_OFFLINE",'Offline');
define("TEXT_TYPE",'Type');
define("TEXT_QUANTITY",'Quantity');
define("TEXT_SHIPPING",'Shipping');
define("LINK_VIEW_LARGE_IMAGE",'View Large Image');
define("LINK_MORE_IMAGES",'More Images');
define("LINK_POST_FEEDBACK",'Post Feedback');
define("LINK_VIEW_PROFILE",'View Profile');
define("BUTTON_MAKE_OFFER",'Make Offer');
define("BUTTON_ADD_ITEM",'Add Item');
define("BUTTON_UPLOAD_MORE",'Upload More');
define("BUTTON_UPDATE",'Update');
define("BUTTON_DELETE",'Delete');

define("TEXT_PURCHASE_ITEM",'Purchase item');
define("MESSAGE_ENTRY_FOR_PURCHASE_CONTACT_ADMIN",'There has been an entry for this purchase. Please contact administrator for details.');
define("ERROR_MISMATCH_DATA_CHECK_STATUS",'There is a mismatch for the data requested. Please check the status of your purchase in \'My Sale Offers\'.');
define("ERROR_CHECK_YOUR_INPUT",'Please check your input!');
define("ERROR_CANNOT_COMPLETE_POINT_LOW",'You cannot completed this purchase as your {point_name} balance is {point}. <br>If you want to continue click the below link');
define("MESSAGE_PAYMENT_MADE_FOR_ITEM",'Payment made for the item.');
define("MESSAGE_CLICK_BELOW_TO_CONTINUE",'If you want to continue click the below link');
define("MESSAGE_SALE_REJECTED_BY_OWNER",'This sale has been rejected by the owner.');
define("MESSAGE_PAYMENT_MADE_BY_CHECK",'Payment made by check for this item.');
define("ERROR_REQUESTED_ITEM_ALREADY_PURCHASED",'Requested {quantity} item already purchased by another user.Please retry!');
define("HEADING_SALES_DETAILS",'Sale Details');
define("TEXT_QUANTITY_AVAILABLE",'Quantity Available');
define("TEXT_QUANTITY_REQUIRED",'Quantity Required');
define("TEXT_INCLUDING_SHIPPING",'Including Shipping');
define("ERROR_ITEMP_POSTED_BY_YOU_CANNOT_PAY",'This item has been posted by you. Hence you cannot proceed for payment.');
define("ERROR_SALE_OFFER_REJECTED_BY_USER",'This sale offer has been rejected by the user.');
define("ERROR_SALE_REJECTED_BY_OWNER",'This sale has been rejected by the owner of the item.');
define("TEXT_TOTAL_AMOUNT",'Total Amount');
define("TEXT_USE_REDEEM",'Use Redeem');
define("TEXT_USE",'Use');

define("HEADING_MY_OFFERS",'Offers Outbox');
define("TEXT_OFFER_TYPE",'Offer Type');
define("TEXT_ACCEPTED",'Accepted');
define("TEXT_REJECTED",'Rejected');
define("TEXT_INVALID",'Invalid');
define("TEXT_INPROGRESS",'InProcess');
define("TEXT_OFFER_SENT",'Offer Sent');
define("TEXT_NEWW",'New Item');
define("MESSAGE_NO_ITEM_SELECTED",'No Item Selected');
define("ERROR_SORRY_NO_OFFERS_MADE",'Sorry No offers made');

define("HEADING_OFFERS_FOR_ME",'Offers Inbox');
define("ERROR_SORRY_NO_OFFERS_RECEIVED",'Sorry No offers received');
define("TEXT_SWAP_OFFER",'Swap Offer');
define("TEXT_WISH_OFFER",'Wish Offer');

define("HEADING_MY_SALES_ORDERS",'Sales Outbox');
define("ERROR_SORRY_NO_ITEMS_TO_DISPLAY",'Sorry No items to display');

define("HEADING_SALES_ORDERS_FOR_ME",'Sales Inbox');
define("HEADING_MY_OFFER_LIST",'My Offer List');
define("HEADING_OFFER_LIST_USER",'Offer List User');
define("HEADING_OFFER_LIST_DETAILED",'Offer List Detailed');
define("HEADING_SALES_OFFERS_FOR_ME",'Sales Offers For Me');
define("HEADING_SALES_LIST_FROM_USER",'SALE LIST FROM {user_name}');
define("HEADING_MY_SALES_OFFERS",'My Sales Offers');

define("ERROR_SELECT_ONLY_ONE_ITEM",'You have to select only one item');
define("HEADING_OFFER_DETAILS",'Offer Details');
define("TEXT_I_WILL_GIVE",'I will give');
define("TEXT_USER_WILL_GIVE",'{user_name} will give');
define("TEXT_USER_WILL_PAY",'{user_name} will pay');
define("TEXT_REQUIRED",'Required');
define("TEXT_ADDITIONAL_INFORMATION",'Additional Information');
define("LINK_VIEW_CONTACT_DETAILS",'View contact details');
define("MESSAGE_STATUS_IS",'The status is {status}');
define("TEXT_WISH_ITEM_DETAILS",'Wish Item Details');
define("TEXT_AMOUNT_REQUIRED",'Amount Required');
define("TEXT_PAYMENT_STATUS",'Payment Status');
define("MESSAGE_STATUS_UPDATED_ON_DATE",'Status updated on {date}');
define("MESSAGE_ITEMS_NOT_DELIVERED_AT_USER_PLACE",'Items not yet delivered at {user_name}\'s place');
define("MESSAGE_NO_ITEMS_TO_DELIVER",'No Items to be delivered');
define("ERROR_SHOULD_SELECT_ITEMS_FOR_SWAPPING",'You should select item(s) for swapping');
define("ERROR_CANNOT_ENTER_VALUES_IN_BOTH_TEXTBOXES",'You cannot enter values in both the text boxes');
define("TEXT_DELIVERABLE",'Deliverable');
define("TEXT_NO_ITEM",'No Item');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_SWAP",'The status of this item has been changed. You cannot swap this item.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_SELL",'The status of this item has been changed. You cannot sell this item.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_ACCEPT",'The status of this item has been changed. You cannot accept this offer.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_OFFER",'The status of this item has been changed. You cannot make an offer.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_EDIT",'The status of this item has been changed. You cannot edit this offer.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_DELETE",'The status of this item has been changed. You cannot delete this offer.');
define("ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_MAKE",'The status of this item has been changed. You cannot make this offer.');
define("ERROR_OFFER_REQUEST_DELETED",'Your offer request has been deleted.');
define("MESSAGE_OFFER_EDITED_SUCCESSFULLY",'Offer edited successfully.');
define("MESSAGE_OFFER_REQUESTED_SUCCESSFULLY",'Your offer request has been sent.');
define("TEXT_I_WILL_PAY",'I will Pay');
define("MESSAGE_USER_SHOULD_PAY_EXTRA_OF",'{user_name} should pay an extra {entity} of');
define("LINK_USE_ESCROW",'Use Escrow');
define("TEXT_USER_WILL_PAY",'{user_name} will Pay');
define("MESSAGE_DELIVERY_STATUS_UPDATED",'Delivery Status updated');
define("TEXT_MY_DELIVERY_STATUS",'My Delivery Status');
define("MESSAGE_ITEMS_DELIVERED_AT_MY_PLACE",'The items were delivered at my place.');
define("MESSAGE_NOT_YET_DELIVERED",'Not yet delivered.');
define("LINK_CLICK_TO_CHANGE_STATUS_TO_DELIVERED",'Click here to change the status to delivered');
define("ERROR_CANNOT_COMPLETE_USERNAME_BALANCE_IS",'You cannot completed this offer as {show_name} {point_name} balance is {points}');
define("ERROR_NO_POINTS_AVAILABLE_IN_USER_ACCOUNT",'No {point_name} available in {show_name} account.');
define("TEXT_I_WILL_DELIVER",'I will deliver');
define("BUTTON_COUNTER_OFFER",'Counter Offer');
define("MESSAGE_STATUS_OF_ITEM_IS_STATUS",'The status of the item is {status}');
define("TEXT_MY_WISH_ITEM_DETAILS",'My wish item details');
define("MESSAGE_PAYMENT_SUBMITTED_FOR_CLEARANCE",'Your payment has been submitted for clearance.Thank you!');
define("TEXT_SWAP_TRANSACTION",'Swap Transaction');
define("TEXT_WISH_TRANSACTION",'Wish Transaction');
define("TEXT_PAYMENT_FOR_FEATURED_SALE",'Payment for featured sale');
define("TEXT_PAYMENT_DONE",'Payment Done');
define("ERROR_ALREADY_OFFERED_AGAINST",'Sorry, you have already one offer against this Item.');
define("ERROR_ITEM_POSTED_BY_YOU",'This item has been posted by you');
define("ERROR_ITEM_NOT_VALID",'The item is not a valid item');
define("ERROR_CANNOT_COMPLETE_THIS_OFFER_REASON",'You cannot complete this offer due to one of the following reasons');
define("HEADING_MAKE_WISH_OFFER",'Make Wish Offer');
define("HEADING_WISH_ITEM_DETAILS",'Wish Item Details');
define("TEXT_AMOUNT_I_WANT",'Amount I Want');
define("TEXT_POINT_I_WANT",'{point_name} I want');
define("TEXT_POINT_OFFERED",'{point_name} offered');
define("ERROR_SELECT_ITEMS_FOR_SWAPPING",'You should select item(s) for swapping');
define("HEADING_OFFER_STATUS",'Offer Status');
define("HEADING_YOUR_INFORMATION",'Your Information');
define("TEXT_DETAILS_OF_COMMUNITATION_GIVEN_BELOW",'The details of communication for the offer is given below');
define("TEXT_OFFER_REJECTED",'The offer has been rejected');
define("TEXT_FULL_NAME",'Full Name');
define("ERROR_ITEM_DETAILS_UNAVAILABLE",'Sorry, item details temporarily unavailable.');
define("HEADING_MAKE_COUNTER_OFFER",'Make Counter Offer');
define("HEADING_ORDER_DETAILS",'Order Details');
define("HEADING_SALE_ORDER_DETAILS",'Sale Order Details');
define("HEADING_SALES_ITEM_DETAILS",'Sale Item Details');
define("ERROR_STATUS_OF_ITEM_REJECTED",'The status of this item is \'Rejected\'');
define("ERROR_CANNOT_REJECT_PAYMENT_DONE",'You cannot reject this order/offer since the payment for this item has been done.');
define("ERROR_CANNOT_REJECT_STATUS_DELETED",'You cannot reject this order since the status of the item is deleted.');
define("ERROR_CANNOT_REJECT_CHECK_SUBMITTED_CONTACT_ADMIN",'You cannot reject this order since the a check has been submitted for approval to the administrator. <br>Contact administrator for more details.');
define("BUTTON_REJECT_ORDER",'Reject order');
define("TEXT_SALE_ITEM_ADDITION",'Sale Item Addition');
define("MESSAGE_ITEM_AVAILABLE_AFTER_ADMIN_VERIFIES_PAYMENT",'Your item would be available for sale once Administrator verifies your payment');
define("TEXT_FEATURED_ITEM_ADDITION",'Featured Item Addition');
define("TEXT_COMMISSION_FOR_ADDITION",'Commission for Addition');
define("TEXT_USE_REEDEEM_POINTS",'Use Redeem {point_name}');
define("TEXT_USE_POINTS",'Use {point_name}');
define("TEXT_USER_GAVE",'{user_name} gave');
define("TEXT_USER_PAID",'{user_name} paid');
define("TEXT_BUYER",'Buyer');
define("TEXT_PENDING_FROM_ESCROW_FOR_USER",'Pending from Escrow for {user_name}');
define("TEXT_ESCROW_FEES",'Escrow Fees');
define("TEXT_AMOUNT_TOTAL_SETTLE",'Toal amount to be settled');
define("TEXT_AMOUNT_TO_SETTLE",'Amount to be Settled');
define("MESSAGE_ITEM_DELETED",'The Item has been deleted');
define("ERROR_ITEM_CANNOT_DELETED",'The Item cannot be deleted');
define("MESSAGE_ITEM_EDITED",'The Item has been edited');
define("MESSAGE_ITEM_CANNOT_EDITED",'The Item cannot be edited');
define("HEADING_SWAP_ITEM_CONFIRM",'Swap Item Confirm');
define("HEADING_SALE_OFFER_DETAILS",'Sale Offer Details');
define("TEXT_AMOUNT_OFFERED",'Amount Offered');
define("MESSAGE_ITEM_NOT_FOUND",'Item not found');
define("HEADING_ESCROW_PAYMENTS",'Escrow Payments');
define("TEXT_POSTING_AMOUNT",'Enter Posting Amount');
define("TEXT_ESCROW_FEES_PERCENTAGE",'Escrow Fee Percentage');
define("BUTTON_CALCULATE",'calculate');
define("TEXT_TOTAL_POINTS", 'Total {point_name}');
define("MESSAGE_POINT_SUCCESSFULLY_DEDUCTED_FROM_ACCOUNT",'{points} {point_name} successfully deducted from your account.');
define("MESSAGE_PROCEED_TO_PAY",'Please proceed to pay the specified amount');
define("OFFERED_TO",'Offered To');
define("OFFERED_BY",'Offered By');

/*Common*/

define("TEXT_WELCOME",'Welcome');
define("TEXT_SEARCH_PRODUCT_NAME",'Search by Product Name');
define("TEXT_LISTING_RESULTS",'Listing {current_rows} of {total_rows} results');
define("LINK_BACK",'Back');
define("TEXT_MANDATORY_FIELDS",'<span>*</span> indicates mandatory fields');
define("TEXT_SEARCH",'Search');
define("MESSAGE_SORRY_NO_RECORDS",'Sorry, no records to display');
define("TEXT_SLNO",'Sl No.');
define("TEXT_ACTION",'Action');
define("TEXT_DATE",'Date');
define("TEXT_MORE",'More');
define("TEXT_AND",'and');
define("TEXT_SIGNIN",'LOGIN');
define("TEXT_SIGNUP",'REGISTER');
//Top Menu
define("MENU_HOME",'Home');
define("MENU_FAQ",'FAQ');
define("MENU_CONTACT",'Contact Us');
define("MENU_HELP",'Help');
define("MENU_SITEMAP",'Sitemap');

//Main Menu
define("MENU_SELL",'Sell');
define("MENU_SALE",'Sale');
define("MENU_SWAP",'Swap');
define("MENU_WISH",'Wish');
define("MENU_REGISTER",'Register');
define("MENU_LOGIN",'Login');
define("MENU_ONLINE_MEMBERS",'Online Members');
define("MENU_CATEGORY_DISPLAY",'Category Display');
define("MENU_REFERRAL",'Referral');
define("MENU_MYBOOTH",'My Booth');
define("MENU_LOGOUT",'Logout');

//Bottom Menu
define("MENU_ABOUT",'About Us');
define("MENU_PRIVACY",'Privacy Policy');
define("MENU_TERMS",'Terms');
define("MENU_TELL_FRIEND",'Tell a Friend');

//Side Menu
define("MENU_ADD_SALE",'Add Item for Sale');
define("MENU_ADD_SWAP",'Add Item for Swap');
define("MENU_ADD_WISH",'Add Wish');
define("MENU_ACC_SUMMARY",'Account Summary');
define("MENU_EDIT_PROFILE",'Edit Profile');
define("MENU_VIEW_MYPROFILE",'View My Profile');
define("MENU_EDIT_PROFILE",'Edit Profile');
define("MENU_RECEIVED_MESSAGES",'Received Messages');
define("MENU_SENT_MESSAGES",'Sent Messages');
define("MENU_VIEW_FEEDBACKS",'View Feedback');
define("MENU_SETTINGS",'Settings');
define("MENU_PLAN_ORDERS",'Plan Orders');
define("MENU_CHANGE_PLAN",'Change Plan');
define("MENU_ADD_REFERRALS",'Add Referrals');
define("MENU_ESCROW_PAYMENTS",'Escrow Payments');
define("MENU_TRACK_SHIPMENT",'Track Shipment');
define("MENU_POINTS",'Points');
define("MENU_BUY",'Buy');
define("MENU_SEND",'Send');
    
define("TEXT_POWEREDBY",'Powered by <a rel="nofollow" href="http://www.iscripts.com/eswap/" target="_blank">iScripts eSwap</a>. A premium product from <a rel="nofollow" href="http://www.iscripts.com" target="_blank">iScripts.com</a>');
//feed
define("HEADING_LATEST_SWAP_FEED",'Latest Swap Feed');

//login
define("ERROR_USERNAME_EMPTY",'Please enter a user name');
define("ERROR_USERNAME_SELECT",'Please select a user name');
define("ERROR_PASSWORD_EMPTY",'Please enter password');
define("BUTTON_LOGIN",'Login');
define("TEXT_INVISIBLE_MODE",'Invisible Mode');
define("LINK_FORGOT_PASSWORD",'Forgot Password');
define("LINK_NEW_USER_SIGNUP_HERE",'New User Sign Up Here');
define("HEADING_LOGIN_HERE",'Login Here');
define("MESSAGE_ACC_ACTIVATION_SUCCESSFULL",'Your account has been activated successfully');
define("MESSAGE_CURRENT_PLAN_EXPIRED",'Your current plan has expired. Please upgrade your plan');
define("MESSAGE_SORRY_NO_ACCESS_NOW",'Sorry, you do not have access now.Please contact {link} for more details');

define("TEXT_SELECT_ONE",'Select One');
define("BUTTON_SEARCH",'Search');
define("BUTTON_RSS_VALID",'Rss Valid');
define("BUTTON_RESET", 'Reset');
define("BUTTON_SUBMIT", 'Submit');
define("BUTTON_SAVE",'Save');
define("BUTTON_DELETE",'Delete');
define("BUTTON_BUY",'Buy');
define("TEXT_YEAR",'Year');


define("TEXT_PAYPAL",'PayPal');
define("TEXT_WORLDPAY",'WorldPay');
define("TEXT_BLUEPAY",'BluePay');
define("TEXT_STRIPE",'Stripe');
define("TEXT_CREDIT_CARD",'Credit Card');
define("TEXT_CASIER_CHECK",'Cashier Check');
define("TEXT_BUSINESS_CHECK",'Business Check');
define("TEXT_MONEY_ORDER",'Money Order');
define("TEXT_WIRE_TRANSFER",'Wire Transfer');
define("TEXT_PERSONAL_CHECK",'Personal Check');
define("TEXT_YOUR_PAY",'Your Pay');
define("TEXT_GOOGLE_CHECKOUT",'Google Checkout');
define("TEXT_AUTHORIZE_NET",'Authorize.net');
define("TEXT_FREE",'Free');
define("LINK_CANCEL",'Cancel');
define("TEXT_STATUS",'Status');
define("TEXT_COMMISSION",'Commission');


define("ERROR_SUBJECT_EMPTY",'Subject can\'t be blank');
define("ERROR_MESSAGE_EMPTY",'Message can\'t be blank');
define("BUTTON_SEND_MESSAGE",'Send Message');
define("TEXT_TITLE",'Title');
define("TEXT_USERNAME",'Username');
define("TEXT_PASSWORD",'Password');
define("TEXT_SUBJECT",'Subject');
define("TEXT_MESSAGE",'Message');
define("LINK_CLOSE",'Close');
define("BUTTON_GO",'Go');
define("TEXT_NO",'No');
define("TEXT_YES",'Yes');
define("LINK_CONTINUE",'Continue');
define("ERROR_QUANTITY_INVALID", 'Quantity required should be a postive numeric value and less than the quantity available');
define("ERROR_CREDIT_CARD_DETAILS_INVALID", 'Please enter a valid credit card number and expiry date');
define("HEADING_PURCHASE_DETAILS", 'Purchase Details');
define("TEXT_ITEM", 'Item');
define("TEXT_CREDIT_CARD_DETAILS", 'Credit Card Details');
define("TEXT_CARD_NUMBER", 'Card Number');
define("TEXT_CARD_VALIDATION_CODE", 'Card Validation Code(CVV2/CVC2/CID)');
define("TEXT_EXPIRATION_DATE", 'Expiration Date (MM/YYYY)');
define("TEXT_BILLING_ADDRESS_DETAILS", 'Billing Address Details');
define("BUTTON_PAY_NOW", 'Pay Now');
define("ERROR_PAYMENT_PROCESS_FAILED", 'Payment process not completed successfully');
define("HEADING_PAYMENT_DETAILS", 'Payment Details');
define("MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE", 'Waiting for the secure payment interface');
define("MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION", 'While using Google CheckOut as payment option, you have to wait for the approval from Administrator of the site for further steps even after successful payment');
define("HEADING_PAYMENT_PROCESS", 'Payment Process');
define("TEXT_MAKE_PAYMENTS_PAYPAL", 'Make payments with PayPal');
define("TEXT_REGISTRATION", 'Registration');
define("ERROR_REFERENCE_EMPTY", 'Please enter a reference number');
define("ERROR_REFERENCE_DATE_NUMBER_EMPTY", 'Please enter a reference number and date');
define("ERROR_GIVEN_INFO_EMPTY_INVALID", 'Given information is empty or invalid');
define("TEXT_DETAILS", 'Details');
define("TEXT_IF_APPLICABLE", 'If Applicable');
define("TEXT_BANK", 'Bank');
define("BUTTON_CONFIRM", 'Confirm');
define("TEXT_PAYMENT_MODE", 'Payment Mode');
define("TEXT_REFERENCE_NUMBER", 'Reference Number');
define("TEXT_MAKE_PAYMENT_WORLDPAY", 'Make payments with WorldPay');
define("CONTACT_ADMIN_GET_ACCOUNT_NUMBER", 'Please contact administrator at {email_link} to get the {site_name} account number for bank wire transfer');
define("HEADING_PAYMENT_STATUS", 'Payment Status');
define("HEADING_PAYMENT_FAILURE", 'Payment Failure');
define("ERROR_PAYMENT_PROCESS_CONTACT_EMAIL", 'There was an error in your payment process. Please contact {site_email} for details');
define("TEXT_PAYMENT_INSTRUCTION", 'Please select from the payment options below for your membership to {site_url}. Keep in mind, if you choose to pay using any method other than  a credit card, google checkout or through PayPal you will not have full access to our services until we are able to verify your payment. Once your payment is verified, this restriction will be lifted and you will be notified automatically via email. Thank you, and enjoy our services');
define("TEXT_PAYMENT_INSTRUCTION_NON_ESCROW", 'Please select from the payment options below for your membership to {site_url}. Keep in mind, if you choose to pay using any method other than  a credit card or through PayPal, you will not have full access to our services until we are able to verify your payment. Once your payment is verified, this restriction will be lifted and you will be notified automatically via email. Thank you, and enjoy our services');
define("TEXT_USE_PAYPAL", 'Use Paypal');
define("TEXT_USE_CREDIT_CARDS", 'Use Credit Cards');
define("TEXT_USE_YOURPAY", 'Use First Data');
define("TEXT_USE_GOOGLE_CHECKOUT", 'Use Google Checkout');
define("TEXT_USE_WORLDPAY", 'Use Worldpay');
define("TEXT_USE_BLUEPAY", 'Use Bluepay');
define("TEXT_USE_CASHIERS_CHECK", 'Use Cashiers Check');
define("TEXT_USE_BUSINESS_CHECK", 'Use Business Check');
define("TEXT_USE_PERSONAL_CHECK", 'Use Personal Check');
define("TEXT_USE_MONEY_ORDER", 'Use Money Order');
define("TEXT_USE_WIRETRANSFER", 'Use Wire Transfer');
define("TEXT_USE_AUTHORIZE", 'Use Authorize');
define("TEXT_USE_STRIPE", 'Use Stripe');
define("TEXT_OTHER_PAYMENTS", 'Other Payments');
define("HEADING_PAYMENT_FORM", 'Payment Form');
define("ERROR_AMOUNT_MISMATCHING_TRY_AGAIN", 'Amount Mismatching. Please try again');
define("TEXT_AMOUNT", 'Amount Per Item');
define("TEXT_FIRST_NAME", 'First Name');
define("TEXT_LAST_NAME", 'Last Name');
define("TEXT_NAME", 'Name');
define("TEXT_EMAIL", 'Email');
define("TEXT_ADDRESS", 'Address');
define("SET_LOCATION", 'Set Location');
define("SET_LOCATION_HERE","Set Location By Clicking Here");
define("TEXT_VIEW_SIMILAR_PRODUCTS","View More Similar Products (Clear Search Radius)");
define("RADIUS_ARRAY",array(5,10,15,20));
define("TEXT_ADDRESS_LINE1", 'Address Line 1');
define("TEXT_ADDRESS_LINE2", 'Address Line 2');
define("TEXT_CITY", 'City');
define("TEXT_STATE", 'State');
define("TEXT_COUNTRY", 'Country');
define("TEXT_ZIP", 'Zip');
define("TEXT_PHONE", 'Phone');
define("TEXT_FAX", 'Fax');
define("TEXT_REFERENCE_DATE",'Reference Date');
define("TEXT_ENTRY_DATE",'Entry Date');
define("TEXT_WAIT_DIRECT_TO_PAYPAL",'Please wait while we redirect you to the Paypal site');
define("TEXT_PURCHASED_DATE",'Purchased Date');
define("ERROR_LOGIN_FIRST_TO_START",'You should login first to start.');
define("TEXT_PAY_SUCCESS_FEE_EACH_TRANSACTION",'Pay Success Fee for Each Transaction');

define("ERROR_TRANSACTION",'There has been an error in the transaction. Please contact administrator for details');
define("MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED",'Thank you for your payment. Your transaction has been completed, and a receipt has been emailed to you.<br>&nbsp;<br> You may visit the "Account Summary " to view details of this transaction.');
define("MESSAGE_SUCCESS_PURCHASED_POINTS",'You have successfully purchased {point_name} by paying an amount of {amount}');
//Payment
define("ERROR", 'Error');
define("ERROR_CARD_HELD_REVIEW", 'The card has been held for review');
define("ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER", 'Communication error with the payment server. Please try after some time');
define("ERROR_CARD_DECLINED", 'The card has been declined');
define("ERROR_FORGOT_NECESSARY_INFORMATION", 'You forgot some necessary information.  Please enter the missing information');
define("MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU", 'Thank you for your payment. Your transaction has been completed, and a receipt has been emailed to you.<br> You may visit the "Account Summary " to view details of this transaction.');
define("MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED", 'Thank you for your payment. Your transaction has been completed');
define("ERROR_MISMATCH_DATA_REQUESTED",'There is a mismatch for the data requested.');
define("MESSAGE_CONTACT_EMAIL_FOR_DETAILS",'Please contact {site_email} for details of this transaction.');
define("MESSAGE_CHECK_PAYMENT_FURTHER_CONTACT_EMAIL",'Please check your payment. For further information please contact {site_email}.');
define("MESSAGE_CHECK_PAYMENT_PROCESS",'Please check your payment process');
define("ERROR_ACCESS_DENIED_CONTACT_EMAIL",'Sorry, this time you do not have access. For more details contact ');
define("ERROR_INVALID_USERNAME_PASSWORD",'Invalid Username or Password!<br> Please retry!');
define("TEXT_MODE",'Mode');
define("LINK_VIEW",'View');
define("LINK_MORE",'More');
define("LINK_BACK_TO_DASHBOARD",'Back to My Booth');
define("LINK_CATEGORIES",'Categories');
define("TEXT_LOGIN_NAME", 'Login Name');
define("TEXT_THANK_YOU",'Thank You');
define("TEXT_N_A",'N/A');
define("LINK_VIEW_DETAILS",'View Details');
define("TEXT_METHOD",'Method');
define("TEXT_PAYMENT_DETAILS",'Payment Details');
define("ERROR_TRANSACTION_ALREADY_PERFORMED_CONTACT_EMAIL",'This transaction has already been performed in the system. Please contact {site_email} for details');
define("HEADING_CONTACT_DETAILS", 'Contact Details');
define("ERROR_POINT_POSITIVE_VALUE",'Please enter a positive numeric value');
define("ERROR_INVALID_LICENCE_KEY",'Invalid License Key');
define("TEXT_CONTACT_EMAIL",'Please contact {email}');
define("MESSAGE_THANKYOU_FOR_PAYMENT",'Thank you for your payment');
define("MESSAGE_PAYMENT_DETAILS_FOLLOWS",'The details of your payment are as follows');
define("TEXT_COMPLETED",'Completed');
define("TEXT_PENDING",'Pending');
define("HEADING_TRANSACTION_DETAILS", 'Transaction Details');
define("TEXT_DELIVERED", 'Delivered');

define("ERROR_EMPTY_DESCRIPTION",'Description cannot be empty');
define("TEXT_LISTING",'Listing');
define("HEADING_POST_FEEDBACK",'Post Feedback');
define("TEXT_MM_DD_YYYY", '(mm/dd/yyyy)');
define("MESSAGE_ARE_YOUR_SURE_TO_DELETE",'Are you sure to delete?');
define("TEXT_PER_ITEM",'per item');
define("TEXT_POSTED_ON",'Posted On');
define("TEXT_POSTED_BY",'Posted By');
define("TEXT_DESCRIPTION",'Description');
define("BUTTON_SEND", 'Send');
define("LINK_CHAT_WITH",'Chat With');
define("ERROR_INVALID_POINT",'{point_name} is invalid');
define("TEXT_PRICE",'Price');

define("MESSAGE_POINT_SUCCESSFULLY_ADDED_TO_ACCOUNT",'{points} {point_name} successfully added to your account.');//newly added
define("ERROR_CANNOT_COMPLETE_NO_POINT_AVAILABLE",'No {point_name} available in your account.');//Edited
define("TEXT_CLICK_LINK_TO_CONTINUE",'If you want to continue click the below link<br>');//Newly added
//define("LINK_SEND_MAIL",'Send A Mail');//deleted

define("ERROR_CANT_CANCEL_PLAN", 'You cannot cancel this subscription plan. Please contact ');//edited spelling
//define("ERROR_FAILURE", 'Failure'you );//deleted
define("MENU_PURCHASE_LIST",'Points Purchase List');//edited
define("ERROR_POINT_INVALID",'{point_name} should be less than the available {point_name}');//edited
define("MESSAGE_CLICK_TO_BUY_POINTS", 'Click "Buy {point_name}" button to buy {point_name}');//newly added
define("ERROR_EMPTY_FEEDBACK", 'Feedback should not be empty');//newly added

define("MESSAGE_THANKYOU_FOR_PAYMENT_WAITING_FOR_ADMIN",'Thank you for your payment. Your transaction has been completed and will be accepted as soon as the administrator accepts the payment.');//<br>&nbsp;<br> You may visit the \'Account Summary\' to view details of this transaction.');//edited
define("ERROR_ENTERED_POINT_LESS_THAN_AVAILABLE",'{point_name} should be less than the available {point_name}');//edited
define("TEXT_YOUR_PENDING_SETTLEMENT_IS",'Click here to view your pending settlement amount');//edited
define("ERROR_SEARCH_TEXT_EMPTY",'Please enter search text');//newly added
define("ERROR_LOGIN_TO_PAY", 'Please login to make the payment');//newly added
define("ERROR_ACCOUNT_DELETED", 'Your account has been deleted. Please contact administrator for more details!');//newly added
define("ERROR_ACCOUNT_DEACTIVATED", 'Your account has been deactivated. Please contact administrator for more details!');//newly added
define("TEXT_CHOOSE_IMAGE",'Choose Image');//newly added
define("BUTTON_ACCEPT",'Accept');//newly added
define("BUTTON_REJECT",'Reject');//newly added
define("ERROR_ENTER_AMOUNT_TO_VIEW_ESCROW_FEE",'Please enter the amount to view the escrow fee');//newly added
define("LINK_DELETE",'Delete');//newly added
define("TEXT_REDEEM_POINTS",'Redeem {point_name}');//newly added
define("TEXT_CHEQUE",'Cheque');//newly added
define("TEXT_DEMAND_DRAFT",'Demand Draft');//newly added
define("TEXT_CASH",'Cash');//newly added
define("MESSAGE_NO_ITEM_AVAILABLE",'No Item Available');//newly added
define("ERROR_AVAILABLE_POINT_IS_LESS",'Available {point_name} is less.');//edited (. added)
define("TEXT_OTHER_USER_DELIVERY_STATUS",'Swap Partner\'s Delivery Status');//edited
define("MESSAGE_ITEMS_DELIVERED_AT_USER_PLACE",'The item(s)delivered at swap partner\'s place');//edited
define("HEADING_OTHER_USERS_INFORMATION",'Swap Partner\'s Information');//edited
define("TEXT_OTHER_USER_WILL_GIVE",'Swap Partner will give');//edited
define("TEXT_OTHER_USER_WILL_PAY",'Swap Partner will pay');//edited
define("English",'English');
define("French",'francés');
define("Spanish",'español');
define("German",'alemán');
define("",'');
define("",'');
define("TEXT_NO_NEW_ADDITIONS",'No new additions available');//edited

define("TEXT_PAYPAL_EMAIL_ERROR","Paypal email is not updated by seller. Please select a different payment option.");

// Responsive Design Integration Changes

define("TEXT_CHANGE_LANGUAGE", "Change Language");
define("TEXT_TOGGLE", "Toggle Navigation");

//Footer Links
define("TEXT_QUICK_LINKS", "Quick Links");
define("TEXT_ABOUT_US", "About Us");
define("TEXT_CONTACT_US", "Contact Us");
define("TEXT_PRIVACY_POLICY", "Privacy Policy");
define("TEXT_TERMS", "Terms");
define("TEXT_FAQ", "Faq");
define("TEXT_HELP", "Help");
define("TEXT_TELL_FRIEND", "Tell a Friend");
define("TEXT_SITEMAP", "Sitemap");
define("TEXT_CASHBACK", "Cashback");
define("TEXT_MOUSE_OVER", "Quick View");
define("NO_PRODUCTS_FOUND", "No Products Found.");
define("NO_ONLINE_MEMBERS_FOUND", "No Online Members Found.");

define("HOME_BANNER_HEADING", "Create Your Own Swapping Site!");
define("HOME_BANNER_TEXT", "But I must explain to you how all this is the ERCO");
define("SEARCH_TEXT", "Search");
define("TEXT_GO", "Go");
define("TEXT_CONFIRM_DELETE", "Are you sure to delete this item?");



define("PAYMENT_INVALID_EXP_MONTH",'Please enter valid expiration month.');
define("PAYMENT_INVALID_EXP_YEAR",'Please enter valid expiration year.');
define("PAYMENT_INVALID_CARD_NUMEBR",'Please enter valid credit card number.');
define("PAYMENT_INVALID_CARD_CODE",'Please enter valid card validation code.');
define("PAYMENT_INVALID_YEAR_FORMAT",'Please enter year in format YYYY.');
define("TEXT_NO_PRICE_SELLER", "Item  price not  updated ,Contact seller to update price.");
define("ITEM_LESS_QTY", "Item quantity less ,Contact seller to update quantity.");

define("DELIVERY_STATUS_CHANGE", "Are you sure to change the delivery status?.");
define("TEXT_SEARCH_RESULTS",'Search results for');
define("LINK_FEEDBACK_ALREDAY_POSTED",'Already Posted');

// New Changed added after Re-testing 
define("MSG_MORE_IMAGE_DELETED_SUCCESSFULLY",'Image removed successfully.');
define("ERROR_ZERO_PRICE",'Price cannot be zero');
define("IMAGE_SELCTION_NOTE",'Select the desired area you like to set as product image.');
define("IMAGE_CROP_VALUE",'Use Selected');
define("UPLOAD_MORE_IMAGES",'Upload more images');
define("CHOOSE_MORE_IMAGE",'More Image');
define("DELETE_IMAGE",'Delete Image');
define("DELETE_IMAGE_CONFIRMATION",'Are you sure you want to delete this image?');
define("IMAGE_DELETED_SUCCESS",'Image deleted successfully.');


define("MANDATORY_FIELDS_COMPULSORY",'Please fill all compulsory fields.');
define("SHIPPING_ADDRESS",'Shipping Address');

define("ERROR_PAYPAL_EMAIL",'Please update your PayPal email in your profile before adding a product.');
define("SEND_MESSAGE_TEXT",'Send Message');
define("REPLY_MESSAGE_TEXT",'Reply');
define("SUB_CATEGORY_TEXT",'Sub-categories can be added inside');
define("ITEM_ADDITION_APPROVAL_TEXT",'Items added through \'Other Payments\' will be listed here for approval.');

// Newly Added For Api
define("INVALID_CREDENTIALS",'Invalid Username or Password');
define("NO_PLANS_FOUND",'No Plans Found');
define("CATEGORY_ID","category ID");
define("CATEGORY_ID_MISSING","category ID is missing on the parameters");
define("MUST_BE_NUMERIC"," on the parameters should be numeric value");
define("CATEGORIES_NOT_FOUND","No Category(s) Found");
define("PRODUCT","The Product");
define("PRODUCT_ID","The product ID");
define("MISSING_PARAMETER"," is missing on the parameters"); 
define("PRODUCT_TYPE","The product type");
define("OTHER_USER_ID","The other user id");
define("USER_ID","The user id");
define("INVALID_STRIPE_KEY","Invalid Stripe Keys Provided");
define("INVALID_SECRET_KEY","Invalid Secret Key");
define("NO_USER_FOUND","No User(s) Found");
define("PRODILE_IMAGE","The profile image");
define("MANDATORY_PARAMETERS","The mandatory parameters are missing on the header");
define("TYPE","The type");
define("CATEGORY","The category");
define("TITLE","The Title");
define("ITEM_DESCRIPTION","The item description");
define("PRICE","The Price");
define("QUANTITY","The Quantity");
define("PRODUCT_ADD_SUCCESS_MSG","Product Added Successfully");
define("INVALID_TYPE_PARAMETER","Invalid value for type parameter");
define("INVALID_POINT","Invalid point value");
define("INVALID_PRICE","Invalid price.");
define("UPDATE_SUCCESS"," Updated Successfully");
define("SALE_ID","Sale id");
define("USER_NOT_FOUND","User not found,Invalid auth token");
define("ERR_ALREADY_PURCHASED","item already purchased by another user.Please retry!");
define("DATE_TXT","Date");
define("TXT_NOT_FOUND"," not found");
define("TXT_SHIPPING_DETAILS","Shipping details");
define("POST_TYPE","Post type");
define("SWAP_ID_NOT_FOUND","Swap id not found,Invalid auth token");
define("CHKSWAP_HIDDEN","chkswap_hidden");
define("CHKSWAP_USER_HIDDEN","chkswap_user_hidden");
define("SUCCESSFULLY_DEDUCTED","successfully deducted from your account.");
define("SUCCESSFULLY_ADDED","successfully added to your account.");
define("SWAP_DETAILS_NOT_FOUND","Swap details not found.");
define("STRIPE_FAILED","Failed to complete stripe transaction");
define("TRANSACTION_AMOUNT_ZERO","Transaction amount is zero");
define("PAYMENT_SUCCESS","Payment Success");
define("NO_MESSAGES_INBOX",'Sorry No Messages in Inbox');
define("NO_MESSAGES_OUTBOX",'Sorry No Messages in Outbox');
define("INVALID_TO_USER","Invalid User . Please check to UserId");
define('EMPTY_LANGUAGE_SET','Sorry No languages available');
define('SEARCH_TEXT_ERROR','Please enter a search text');
?>
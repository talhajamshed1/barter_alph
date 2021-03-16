<?php

// +----------------------------------------------------------------------+
// | Copyright (c) 2008  Christopher Kois                                 |
// +----------------------------------------------------------------------+
// | This source file is subject to the GPL license, That is bundled      |
// | with this package in the file LICENSE, and is available through      |
// | the world-wide-web at                                                |
// | http://www.gnu.org/licenses/gpl.html                                 |
// +----------------------------------------------------------------------+
// | Author: Christopher Kois <cpkois@cpan.org>                           |
// +----------------------------------------------------------------------+
//
/**
 * Validation methods for credit card related data
 *
 * @category   Services
 * @package    Services_Bluepay
 * @author     Christopher Kois <cpkois@cpan.org>
 * @copyright  2008  Christopher Kois
 * @license    http://www.gnu.org/licenses/gpl.html  GPL
 */

/**
 * Bluepay 2.0 Gateway Interface Class
 *
 * This class provides methods to post credit card transactions to the Bluepay gateway.
 * NOTE: There is no parameter error checking.  This must occur on the user's end.
 *       This package was written in and tested with PHP5.
 *
 * @category   Web Services
 * @package    Bluepay_Bluepay20Post
 * @author     Christopher Kois <cpkois@cpan.org>
 * @copyright  2008  Christopher Kois
 * @license    http://www.gnu.org/licenses/gpl.html  GPL
 * @version    Release: 0.0.1
 */
class Services_Bluepay {

    /**
     * The transaction data for the post and response
     *
     * @access private
     * @var    array $_postData
     */
    var $_postData = array();
    /**
     * The URL where transactions should be posted
     *
     * @access private
     * @var    string $_url
     */
    var $_url = 'https://secure.bluepay.com/interfaces';
    /**
     * The mode of the transaction (TEST or LIVE), TEST is default
     *
     * @access private
     * @var    string $_mode
     */
    var $_mode = "LIVE";

    /**
     * Sets the specified value
     *
     * @access public
     * @param  string $field name
     * @param  string $value value
     * @return void
     * @see    getValue()
     */
    public function setValue($field, $value) {
        $this->_postData[$field] = $value;
    }

    /**
     * Gets the specified value
     *
     * @access public
     * @param  string $field name
     * @return void
     * @see    setValue()
     */
    public function getValue($field) {
        return $this->_postData[$field];
    }

    /**
     * Run transaction using bp20post
     * Generates tamper proof seal and posts the transaction to the Bluepay Gateway
     *
     * @access public
     * @param  string $field name
     * @return void
     */
    public function bp20post($ttMode) {
        // Update MODE (if unset)
        if (isset($this->postData['MODE'])) {

        } else {
            $this->_postData['MODE'] = $_mode;
        }

        //reassign mode value
        $this->_postData['MODE'] = $ttMode;

        /* calculate the tamper proof seal */
        $tamper_proof_data = $this->_postData['SECRET_KEY'] . $this->_postData['ACCOUNT_ID'] .
                $this->_postData['TRANS_TYPE'] . $this->_postData['AMOUNT'] . $this->_postData['MASTER_ID'] .
                $this->_postData['NAME1'] . $this->_postData['PAYMENT_ACCOUNT'];
        $this->_postData['TAMPER_PROOF_SEAL'] = bin2hex(md5($tamper_proof_data, true));
        unset($this->_postData['SECRET_KEY']); // REMOVE SECRET_KEY FROM POST STRING

        /* perform the transaction */
        $post = curl_init();
        curl_setopt($post, CURLOPT_URL, $this->_url . '/bp20post'); // Set the URL
        curl_setopt($post, CURLOPT_USERAGENT, "BluePay20Post PHP/2.0");  // Information on interface
        curl_setopt($post, CURLOPT_POST, 1); // Perform a POST
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);  // If not set, curl prints output to the browser
        curl_setopt($post, CURLOPT_POSTFIELDS, http_build_query($this->_postData));
        $response = curl_exec($post);
        curl_close($post);

        /* parse response and merge with existing data */
        $responseArray = array();
        parse_str($response, $responseArray);
        $this->_postData = array_merge($this->_postData, $responseArray);
    }

}

//checking demo
switch (DisplayLookUp('bluepaydemo')) {
    case 'YES':
        $paymentMode = 'TEST';
        break;

    case 'NO':
        $paymentMode = 'LIVE';
        break;
}//end switch
//call function
$bp = new Services_Bluepay();
$bp->setValue("ACCOUNT_ID", DisplayLookUp('bluepayid'));
$bp->setValue("SECRET_KEY", DisplayLookUp('bluepaykey'));
$bp->setValue("TRANS_TYPE", 'SALE');
$bp->setValue("MODE", $paymentMode); # Default is TEST --> Set to LIVE for live tx
$bp->setValue("AMOUNT", round($cost, 2));  # ODD returns Approved, EVEN returns Declined in TEST mode
$bp->setValue("PAYMENT_ACCOUNT", $CardNum); # VISA Test Card
$bp->setValue("CARD_EXPIRE", $Month . $Year);
$bp->setValue("NAME1", $FirstName);
$bp->setValue("NAME2", $LastName);
$bp->setValue("PHONE", $_SESSION["gphone"]);
$bp->setValue("EMAIL", $Email);
$bp->setValue("COMPANY_NAME", SITE_NAME);
$tamperProofSeal = md5(DisplayLookUp('bluepaykey') . DisplayLookUp('bluepayid') . round($cost, 2) . $paymentMode);
$bp->setValue("TAMPER_PROOF_SEAL", $tamperProofSeal);
$bp->setValue("ADDR1", $Address);
$bp->setValue("CITY", $City);
$bp->setValue("STATE", $State);
$bp->setValue("ZIP", $Zip);
$bp->setValue("COUNTRY", $Country);
$bp->bp20post($paymentMode);

//checking response
if ($bp->getValue("STATUS") == '1' && $bp->getValue("MESSAGE") == 'Approved Sale') {
    $cc_flag = true;    
    $cc_tran = $bp->getValue("TRANS_ID");
    if($cc_tran==0 && $paymentMode=='TEST') $cc_tran = rand (1000, 10000);
}//end if
else {
    $cc_flag = false;
    $cc_err = $bp->getValue("MESSAGE");
}//end else
?>

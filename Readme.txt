 ******************************************************************************************
                        iScripts eSwap2.4.2 Swapping Platform  Readme File

                                 Oct 23, 2020
 ******************************************************************************************
 
 (c) Copyright Armia Systems,Inc 2005-15. All rights reserved.



 This file contains information that describes the installation of iScripts eSwap Platform 





 ******************************************************************************************

 Contents

 ******************************************************************************************

 1.0 Introduction

 2.0 Requirements
 
 3.0 Installing iScripts eSwap Platform

 4.0 Upgrading from version 2.2 iScripts eSwap Platform
 
 4.1 Upgrading from version 2.3 iScripts eSwap Platform

 5.0 Setting up a cron

 6.0 Adding a custom theme





 ******************************************************************************************

 1.0 Introduction

 ******************************************************************************************

 This file contains important information you should read before installing eSwap Swapping 

 Platform.



 The eSwap Swapping Platform enables any web master to create a barter exchange network 

 website from his/her server.

 For any support visit us at http://www.iscripts.com/support/kb/



 ******************************************************************************************

 2.0 Requirements

 ******************************************************************************************

 The eSwap Swapping Platform is developed in PHP and the database is MySQL. 


 
 The requirements can be summarized as given below:

	1. PHP >= 5.0

		You can get the latest PHP version at http://www.php.net

	2. MySQL >= 5.x

	3. Curl Support (In case you are using Authorize.net payment gateway)

	4. Open port 1129 (In case you are using First Data payment gateway)



 Other Requirements For Trouble free Installation/Working


	* SendMail - (Yes)

	* PHP safe mode - (OFF)

	* PHP open_basedir - (OFF)

	* CURL extension - (Yes)





 ******************************************************************************************

 3.0 Installing iScripts eSwap Platform

 ******************************************************************************************

 3.1) Unzip the entire contents to a folder of your choice.


	a) Upload the contents to the server to the desired folder using an FTP client.  
           If you do not have a FTP client we suggest CoreFTP or FTPzilla


 In most cases, depending on hosting company, the browser accessable folder is often listed as 
 public_html. All files should be uploaded in this location. For example: yoursite.com/public_html/eswap



 If no folder is required for public browser view, install to the root level or a folder of your choice



 3.2) Set 'Write' permission (777 for linux server) to the following files/folders.

	a) pics

	b) images

	c) banners

	d) help

	e) pem
	
	f) pics/profile

	g) lang_flags
	
	h) sliders


 3.3) Provide 'Write' permission (777 for linux server) for the following files.

	a) includes/config.php




 3.3.1) Go to hosting control panel and create mySQL database. Contact your hosting company for details. 

        Record username and password for mySQL database for input to your iScripts installation.




 3.4) Run the following URL in your browser and follow the instructions.


		
	http://www.yoursitename/install/


		
	If you have uploaded the files in the root (home directory), you can access the	iScripts eSwap 	
	install wizard at http://www.yoursitename


		
	You can also install the script in any directory under the root. For example if you have
        uploaded the zip file in a directory like http://www.yoursitename/eSwap then you can access 		
        the eSwap site at http://www.yoursitename/eSwap
		


	In the installation step, please provide the site url as described above, without any 
	trailing slashes.


	
	Make sure you enter the same license key you received at the time of purchase,in the 
        "License Key" field. The script would function only for the domain it is licensed. If you cannot recall 	
        the license its also included in the email you received with subject: �iScripts.com software 		
        download link� . You can also get the license key from 	your user panel at www.iscripts.com



 3.5) Remove the 'Write' permission provided to the file 'includes/config.php'.



 3.6) Delete the 'install' folder. 



 3.7) Change the settings to match your specific requirements at http://www.yoursitename/admin/
	
	Login using the admin username and password provided during installation.






 ******************************************************************************************

 4.0 Upgrading from version 2.2 iScripts eSwap Platform

 ******************************************************************************************
 
 Note: You will lose all customizations, that you have already done.



 4.1) Download the new version of eSwap.


 4.2) Take backup of the existing all files and database.


 4.3) Replace all your current files with the new one except pics, banners, pem, your logo(logo in images folder)

     and includes/config.php file.


 4.4) Run the URL http://www.yoursitename/upgrade2.3/ and follow the instructions shown on screen. 	


******************************************************************************************

 4.1 Upgrading from version 2.3 iScripts eSwap Platform

 ******************************************************************************************
 
 Note: You will lose all customizations, that you have already done.



 4.1 .1) Download the new version of eSwap.


 4.1 .2) Take backup of the existing all files and database.


 4.1 .3) Replace all your current files with the new one except pics, banners, pem, your logo(logo in images folder)

     and includes/config.php file.


 4.1 .4) Run the URL http://www.yoursitename/upgrade2.4/ and follow the instructions shown on screen. 




 ******************************************************************************************
 
 5.0 Setting cron jobs

 ******************************************************************************************
	
 5.1) To enable subscription using authorize.net set cron jobs/scheduled tasks to 

	run the following files at 1 A.M daily.



	a)  http://www.yoursitename/cron_authorize_alert.php



          an example could be  

	    /usr/bin/php -q /home/eswap2.4/public_html/yourinstallationfolder/cron_authorize_alert.php




 5.2) To clean not completed sale set cron jobs/scheduled tasks to 

	run the following files at 1 A.M daily.



	a)  http://www.yoursitename/cron_upate_sale_quantity_not_purchased.php



          an example could be  

	    /usr/bin/php -q /home/eswap2.4/public_html/yourinstallationfolder/cron_upate_sale_quantity_not_purchased.php



 5.3) To clean online users set cron jobs/scheduled tasks to 

	run the following files at 1 A.M daily.



	a)  http://www.yoursitename/cron_online_users.php



          an example could be  

	    /usr/bin/php -q /home/eswap2.4/public_html/yourinstallationfolder/cron_online_users.php




 ******************************************************************************************

 6.0 Adding a custom theme

 ******************************************************************************************

 6.1) Create a new folder with the name of the new theme in the themes folder



 6.2) Copy the contents of an existing theme to this new folder and alter them

      to suit your needs



 6.3) The copied style.css could be edited to give a the appearnce changes



 6.4) Replace the image preview.gif with the new preview image. 
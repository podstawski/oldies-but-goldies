Encoding=ISO-8859-2

##################################################################
# Upload-settings
##################################################################

# We want to use POST
Upload.Http.Method=post

# Location of our upload-script
Upload.URL.Action=./jupload.php

# We do not need a login
Upload.Auth.UserAuthRequired=false

# We want to redirect when finished
Upload.Complete.URL=jupload.php?close=1
Upload.Complete.Target=JUpload
Upload.Http.Cookies=<?="WKSESSID=".$_COOKIE['WKSESSID']; ?>



Gui.Status.ShowSuccessDialog=false
Gui.ServerResponse.AutoShow=false
Gui.ServerResponse.Enable=false
Gui.ServerResponse.Height=100

Locale=<?

	switch ($_GET['lang'])
	{
		case 'pl':
		case 'i':
		case 'p':
			echo 'pl';
			break;

		default:
			echo 'en';
			break;
	}

?>



# The minimum required JAVA Version. If the user does not have this version, he is asked to go to Sun's download page and install it.
MinJavaVersion=1.4

# Increased verbosity and information-output?
Debug=true


Gui.ContextMenu.Files=AddFolder,Seperator,CopyClipboard,PasteClipboard,Seperator,RenameFile,SaveFiles,DeleteFiles
Gui.ContextMenu.General=ShowInvalids,Options,Seperator,Screenshot,JUploadScreenshot,ScreenshotDelay,Seperator,About
Gui.Toolbar.Buttons=add,remove,upload

Upload.Http.MaxRequestSize=83886080
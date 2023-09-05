This folder contains the files to replace in your eFiction install to bridge it with 
an installation of SMF.  These files will let you use your SMF members 
table as the authors table for eFiction.  This bridge assumes your SMF intall 
and your eFiction install share the same database.

Move the following files to the user/ folder in your eFiction install:

editbio.php
login.php
profile.php
register.php
logout.php

Move the following files to the includes/ folder in your eFiction install:

get_session_vars.php
queries.php

Move the en_SMF.php file to the languages/ folder in your eFiction install.

Edit queries.php to point to your SMF tables and directory.

Goto Admin->Page Links and edit your Register and Lost Password links to point to your 
SMF pages.

Register -> PATHTOSMF/index.php?action=register
Lost Password -> PATHTOSMF/index.php?action=reminder

If eFiction is a sub-folder under SMF PATHTOSMF will be ../
If they are siblings the path will be ../SMFFOLDER/ where SMFFOLDER is tha name of SMF's folder.
If SMF is a subfolder if eFiction PATHTOSMF will be SMFFOLDER/ where SMFFOLDER is tha name of SMF's folder.

Move QueryString.php to the Sources folder of your SMF installation. Note this is the QueryString.php 
from version 1.1.2 of SMF.




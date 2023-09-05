This folder contains the files to replace in your eFiction install to bridge it with 
an installation of PHP-Fusion.  These files will let you use your PHP-Fusion users 
table as the authors table for eFiction.  This bridge assumes your PHP-Fusion intall 
and your eFiction install share the same database.

Move the following files to the users/ folder in your eFiction install:

editbio.php
login.php
profile.php
register.php
lostpassword.php

Move the following files to the includes/ folder in your eFiction install:

get_session_vars.php
queries.php

Edit queries.php to point to your PHP-Fusion tables.

Goto Admin->Page Links and edit your Register and Lost Password links to point to your 
PHP-Fusion pages.

Register -> PATHTOPHPFUSION/register.php 
Lost Password -> PATHTOPHPFUSION/lostpassword.php

If eFiction is a sub-folder under PHP-Fusion PATHTOPHPFUSION will be ../




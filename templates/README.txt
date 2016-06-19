=================
TEMPLATES INFO:
=================

The template files in the 'html' folder are not parsed by Savant, but simply loaded into a string and echoed
to the browser via Savant. This makes for easier editing as it keeps HTML data from PHP files.

The code between braces (ie: {name}) in the '.htm' files gets parsed when the system runs. You should be careful not to 
remove some of these as you may find that certain data does not appear correctly. Advanced users should be able to see
which vars are important. Many simply load language data, others script data.

If you edit and find something has gone wrong, re-download the script and replace these files.

IMPORTANT: Unlike the '.tpl.php' template files, which are directly parsed by Savant, the '.htm' template files cannot
have any PHP code inserted into them directly. PHP will not work if you do this.

ALL language loads from the 'lang/english.php' file. Advanced users can edit the vars directly in the templates.

E-Mail templates can be found in the 'templates/email/' folder. If you need to edit any language in an e-mail template,
edit the .txt file in this folder.

All colour attributes are rendered in the stylesheets (CSS) in the root of your installation.

----------------------------------------------------------------
Maian Script World
http://www.maianscriptworld.co.uk

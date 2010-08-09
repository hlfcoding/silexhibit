<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Translation
*
* English U.S.
* 
* @version 1.0
* @author Vaska
*/

// EN-US

$words = array 
(
	// BASIC INTERFACE PARTS
	'indexhibit' => 'Indexhibit',
	'preferences' => 'Preferences',
	'help' => 'Help',
	'logout' => 'Logout',
	
	// BASIC MAIN NAV PARTS
	'content' => 'Content',
	'admin' => 'Admin',
	'pages' => 'Pages',
	'settings' => 'Settings',
	'section' => 'Section',
	'exhibits' => 'Exhibits',
	'stats' => 'Statistics',
	'settings' => 'Settings',
	'system' => 'System',
	'user' => 'User',
	
	// error messages
	'login err' => 'Your password or login are incorrect. Try again.',
	'router err 1' => 'That module/page does not exist.',
	'class not found' => 'Class not found',
	'database is unavailable' => 'Database is unavailable.',
	'error finding settings' => 'Error finding settings.',
	'no menu created' => 'No menu was created.',
	'no results' => 'No results, there was an error.',
		
	// Location descriptors
	'main' => 'Main',
	'edit' => 'Edit',
	'preview' => 'Preview',
	'search' => 'Search',
	'new' => 'New',
	
	// some tabs
	'text' => 'Text',
	'images' => 'Images',
	
	// meed tp tranlsate the default sections
	'project' => 'project',
	'on-going' => 'ongoing',
	
	// generic forms parts & labels
	'page title' => 'Exhibition Name',
	'add page' => 'Add Exhibition',
	'submit' => 'Submit',
	'update' => 'Update',
	'required' => 'Required',
	'optional' => 'Optional',
	'hidden' => 'Hidden',
	'delete' => 'Delete',
	'publish' => 'Publish',
	'unpublish' => 'Unpublish',
	'choose file' => 'Choose File',
	
	'exhibition name' => 'Exhibition Name',
	'advanced mode' => 'Advanced Mode',
	'theme' => 'Theme',
	'api key' => 'API Key',
	'image max' => 'Image Max Size',
	'thumb max' => 'Thumbnail Max Size',
	'image quality' => 'Image Quality',
	'freelancer' => 'Freelance Status',
	'pre nav text' => 'Pre-Nav Text',
	'post nav text' => 'Post-Nav Text',
	'html allowed' => '(HTML Allowed)',
	'update order' => 'Update Order',
	'view' => 'View',
	'no images' => 'No Images',
	'add images' => 'Add Images',
	'image title' => 'Image Title',
	'image caption' => 'Image Caption',
	'attach more files' => 'Attach More Files',
	'page process' => 'Process Page',
	'page hide' => 'Hide Page',
	'background image' => 'Background Image',
	'background color' => 'Background Color',
	'edit color' => 'Click color box to edit selection.',
	'uploaded' => 'Uploaded',
	'updated' => 'Updated',
	'width' => 'Width',
	'height' => 'Height',
	'kb' => 'KB',
	'saving' => 'Saving...',
	
	// editor buttons & such
	'bold' => 'Bold',
	'italic' => 'Italic',
	'underline' => 'Underline',
	'links manager' => 'Links Manager',
	'files manager' => 'Files Manager',
	'save preview' => 'Save/Preview',
	'upload' => 'Upload',
	'make selection' => 'Make Selection',
	'on' => 'On',
	'off' => 'Off',
	
	// popup editor parts
	"create link" => "Create link",
	"hyperlink" => "Hyperlink",
	"urlemail" => "URL / Email",
	"none found" => "None found",
	"allowed filetypes" => "Allowed Filetypes",
	"upload files" => "Upload Files",
	"attach more" => "Attach More Files",
	"title" => "Title",
	"edit file info" => "Edit File Information",
	"description" => "Description",
	"if applicable" => "(if applicable)",
	
	// statistics related things
	'referrers' => 'Referrers',
	'page visits' => 'Page Visits',
	
	'total' => 'Total',
	'unique' => 'Unique',
	'refers' => 'Refers',
	
	'since' => 'Since',
	'ip' => 'IP',
	'country' => 'Country',
	'date' => 'Date',
	'keyword' => 'Keyword',
	'total pages' => 'total pages',
	'next' => 'Next',
	'previous' => 'Previous',
	'visits' => 'Visits',
	'page' => 'Page',
	
	'this week' => 'This Week',
	'today' => 'Today',
	'yesterday' => 'Yesterday',
	'this month' => 'This Month',
	'last week' => 'Last Week',
	'year' => 'Year',
	'last month' => 'Last Month',
	'top 10 referrers' => 'Top 10 Referrers',
	'top 10 keywords' => 'Top 10 Keywords',
	'top 10 countries' => 'Top 10 Countries',
	'past 30' => 'past 30 days',
	
	'2 weeks ago' => '2 weeks ago',
	'3 weeks ago' => '3 weeks ago',
	'4 weeks ago' => '4 weeks ago',
	'2 days ago' => '2 days ago',
	'3 days ago' => '3 days ago',
	'4 days ago' => '4 days ago',
	'5 days ago' => '5 days ago',
	'6 days ago' => '6 days ago',
	'2 months ago' => '2 months ago',
	'3 months ago' => '3 months ago',
	'4 months ago' => '4 months ago',
	'5 months ago' => '5 months ago',
	'6 months ago' => '6 months ago',
	'7 months ago' => '7 months ago',
	'8 months ago' => '8 months ago',
	'9 months ago' => '9 months ago',
	'10 months ago' => '10 months ago',
	'11 months ago' => '11 months ago',
	
	// system strings
	'name' => 'Name',
	'last name' => 'Last Name',
	'email' => 'Email',
	'login' => 'Login',
	'password' => 'Password',
	'confirm password' => 'Confirm Password',
	'number chars' => '6-12 chars',
	'if change' => 'if changing',
	'time now' => 'What time is it now?',
	'time format' => 'Time Format',
	'your language' => 'Language',
	
	// installation
	'htaccess ok' => '.htaccess file is ready...',
	'htaccess not ok' => "Set 'MODREWRITE' to 'false' in config.php...",
	'files ok' => "/files folder is writable...",
	'files not ok' => "/files folder is not writable...",
	'filesgimgs ok' => "/files/gimgs folder is writable...",
	'filesgimgs not ok' => "/files/gimgs folder is not writable...",
	'try db setup now' => "Let's try to set up the database now.",
	'continue' => "Continue",
	'please correct errors' => "Please correct the above errors.",
	'refresh page' => "Then refresh this page.",
	'goto forum' => "Go to the <a href='http://www.indexhibit.org/forum/'>help forum</a> for assistance.",
	'edit config' => "You need to edit your config.php file with the proper database settings.",
	'refresh this page' => "Refresh this page after you have done this step.",
	'contact webhost' => "If you do not know these contact your webhost for assistance.",
	'database is ready' => "Looks like the database is ready.",
	'tried installing' => "We tried to install the tables.",
	'cannot install' => "We cannot connect or install the database.",
	'check config' => "Please check your config settings again.",
	'default login' => "The default login / password is index1 / exhibit.",
	'change settings' => "Change these and your site settings once inside.",
	'lets login' => "Let's login!",
	'freak out' => "There is some horrific error - freak out!",
	
	// javascript confirm pops
	'are you sure' => 'Are you sure?',
	
	
	// additions 17.03.2007
	'change password' => 'Change Password',
	'project year' => 'Project Year',
	'report' => 'Reported',
	'email address' => 'Email Address',
	'below required' => 'Below required for Indexhibit reporting',
	'from registration' => 'from Indexhibit registration',
	'register at' => 'Register at',
	'background tiling' => 'Background Tiling',
	'page process' => 'Process HTML',
	'hide page' => 'Hide Page from Index',
	'allowed formats' => 'jpg, png and gif only.',
	'filetypes' => 'Filetypes',
	'updating' => 'Updating...',
	
	// additions 18.03.2007
	'max file size' => 'Max File Size',
	'exhibition format' => 'Exhibition Format',
	'view full size' => 'View full size',
	'cancel' => 'Cancel',
	'view site' => 'View Your Site',
	'store' => 'Store',
	
	// additions 19.03.2007
	'config ok' => "/ndxz-studio/config folder is writable...",
	'config not ok' => "/ndxz-studio/config folder is not writable...",
	'database server' => "Database Server",
	'database username' => "Database Username",
	'database name' => "Database Name",
	'database password' => "Database Password",
	
	// additions 10.04.2007
	'create new section' => "Create New Section",
	'section name' => "Section Name",
	'folder name' => "Folder Name",
	'chronological' => "Chronological",
	'sectional' => "Sectional",
	'use editor' => "WYSIWYG Editor",
	'organize' => "Organize",
	'sections' => "Sections",
	'path' => "Path",
	'section order' => "Section Order",
	'reporting' => "Reporting",
	'sure delete section' => "Are you sure? This will delete all pages in this section too.",
	'projects section' => "Projects Section",
	'about this site' => "About This Site",
	'additional options' => "Additional Settings",
	'add section' => "Add Section",
	
	// additions 21.04.2007
	'section display' => "Display Section Title",
	
	// additions - no date yet
	'invalid input' => "Invalid Input",
	
	'the_end' => 'the end'
	//'' => '',
);


?>
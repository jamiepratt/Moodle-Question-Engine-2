<?php

////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////


$THEME->sheets = array('reset', 'colors');

/// This variable is an array containing the names of all the
/// stylesheet files you want included in this theme, and in what order
////////////////////////////////////////////////////////////////////////////////


$THEME->standardsheets = array('styles_layout');

/// This variable can be set to an array containing
/// filenames from the *STANDARD* theme.  If the
/// array exists, it will be used to choose the
/// files to include in the standard style sheet.
/// When false, then no files are used.
/// When true or NON-EXISTENT, then ALL standard files are used.
/// This parameter can be used, for example, to prevent
/// having to override too many classes.
/// Note that the trailing .css should not be included
/// eg $THEME->standardsheets = array('styles_layout','styles_fonts','styles_color');
////////////////////////////////////////////////////////////////////////////////


$THEME->parent = '';

/// This variable can be set to the name of a parent theme
/// which you want to have included before the current theme.
/// This can make it easy to make modifications to another
/// theme without having to actually change the files
/// If this variable is empty or false then a parent theme
/// is not used.
////////////////////////////////////////////////////////////////////////////////


$THEME->parentsheets = false;

/// This variable can be set to an array containing
/// filenames from a chosen *PARENT* theme.  If the
/// array exists, it will be used to choose the
/// files to include in the standard style sheet.
/// When false, then no files are used.
/// When true or NON-EXISTENT, then ALL standard files are used.
/// This parameter can be used, for example, to prevent
/// having to override too many classes.
/// Note that the trailing .css should not be included
/// eg $THEME->parentsheets = array('styles_layout','styles_fonts','styles_color');
////////////////////////////////////////////////////////////////////////////////


$THEME->pluginsheets = array('mod', 'block', 'format', 'gradereport');

/// Which types of plugins should be searched for a styles.php file. This
/// Allows plugins to include enough styling information to work out of the box.


$THEME->metainclude = true;

/// When this is enabled (or not set!) then Moodle will try
/// to include a file meta.php from this theme into the
/// <head></head> part of the page.


$THEME->standardmetainclude = true;

/// When this is enabled (or not set!) then Moodle will try
/// to include a file meta.php from the standard theme into the
/// <head></head> part of the page.


$THEME->parentmetainclude = false;

/// When this is enabled (or not set!) then Moodle will try
/// to include a file meta.php from the parent theme into the
/// <head></head> part of the page.


$THEME->navmenuwidth = 50;

/// You can use this to control the cutoff point for strings
/// in the navmenus (list of activities in popup menu etc)
/// Default is 50 characters wide.


$THEME->makenavmenulist = false;

/// By setting this to true, then you will have access to a
/// new variable in your header.html and footer.html called
/// $navmenulist ... this contains a simple XHTML menu of
/// all activities in the current course, mostly useful for
/// creating popup navigation menus and so on.


$THEME->resource_mp3player_colors =
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';

/// With this you can control the colours of the "big" MP3 player
/// that is used for MP3 resources.


$THEME->filter_mediaplugin_colors =
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'waitForPlay=yes';

/// ...And this controls the small embedded player


$THEME->custompix = true;

/// If true, then this theme must have a "pix"
/// subdirectory that contains copies of all
/// files from the moodle/pix directory, plus a
/// "pix/mod" directory containing all the icons
/// for all the activity modules.
////////////////////////////////////////////////////////////////////////////////


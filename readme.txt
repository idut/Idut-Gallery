Idut Gallery 2.1 (beta)
www.idut.co.uk

README CONTENTS:
---------------
  1. Description
  2. Installation
   I.    Web Install
   II.   Clean Install
   III.  Upgrade
   IIII. Advanced Configuration
  3. System Requirements
  4. Version History
  5. License


1. DESCRIPTION
--------------
Idut Gallery is a simple, but very powerful PHP gallery system. 
It does not use a database. It can be easily customised and 
incorporated into an existing website or on its own. The gallery 
consists of only 3 files including a powerful admin script where 
you can manage galleries and change settings.

Please report all bugs and suggestions to http://www.idut.co.uk

Please link to the Idut Gallery website on your site or leave the 
"Powered by Idut Gallery" link in. Thanks.


2. INSTALLATION
---------------
  2. I. Web Install
  -------------------
  Save yourself time and effort and install Idut Gallery 2 straight from www.idut.co.uk to
  your website! Just visit idut.co.uk

  2. II. Clean Install
  -------------------
  To install Idut Gallery from scratch:
     1. Unzip all files
     2. Upload files to web directory
     3. Chmod 777 the config.php and mainfile.html files
     4. Chmod 777 the directories that will store the folders containing images and thumbnails (images/ and thumbnails/)
     5. Access admin.php online to create galleries and change settings
          Username: demo. Password: demo - change these in config.php before uploading
     6. Access gallery online through index.php

  2. III. Upgrade
  --------------
  To UPGRADE Idut Gallery from a previous version:
     Upgrade from version 1:
          1. Idut Gallery 2 is not fully compatable with version 1
          2. Follow instructions above and do a clean install but keep your images directory
          3. For more upgrade information, visit www.idut.co.uk
     Upgrade from version 2.0:
          1. Replace your old index.php and admin.php with the latest version
          2. Upload .htaccess, thinupload.php and thinupload.js to your main gallery folder
          3. To activate pretty URLS, insert into config.php the following line:
               $IG_CONFIG['prettyurls'] = true;
          4. Create directory called "temp" and chmod 777.

  2. IIII. Advanced Configuration
  ------------------------------
  To have Idut Gallery fit more seamlessly into your website you should 
  edit config.php and template.php files in a text editor before uploading the gallery.
  Please include a link to http://www.idut.co.uk in the gallery.


3. SYSTEM REQUIREMENTS
----------------------
   - PHP 4 or newer (php.net)
   - GD Graphics Library (libgd.org)


4. VERSION HISTORY
------------------

Idut Gallery 2.1 (beta) - 10 May 2008
   - FIXED: All albums will be shown on main page
   - NEW/FIXED: Universal sorting works a lot better now
   - UPDATED: Enhanced security, upgrade is strongly recommended
   - NEW: Ability to select the album cover within admin.php
   - NEW: Ability to bulk upload and client-side resize using Java and Thin Upload
   - NEW: Pretty URLs using HTACCESS. Activate in config.php by adding the line: $IG_CONFIG['prettyurls'] = true; 

Idut Gallery 2.0 (beta) - 11 August 2007
   - NEW: Change of style to make it easier to integrate into your website
   - FIXED: Various improvements to page numbering
   - FIXED: Improved security of main file
   - UPDATED: Enhanced Administration login
   - NEW: Album descriptions, image descriptions and comments on images
   - NEW: Ability to upload images using admin page as well as traditional FTP
          These files can be reduced to a given size and a watermark can be added.
   - UPDATED: Enhanced settings administration
   - NEW: Idut Gallery now supports PNG and GIF files as well as JPEG.
   - NEW: Plug-ins add extra functionality to your gallery...
           - Human Checker - reduce comment spam
           - Reflection.js - add reflections to your images
   - NEW: Install Idut Gallery straight from www.idut.co.uk

Idut Gallery 1.1 (beta) - 01 January 2006
   - NEW: Config, contents and style files can now be changed from the admin section
   - UPDATED: Admin login now more user friendly and more system compatible
   - NEW: Standard fixed size thumbnails option using crop/resize
   - NEW: Can show thumbnail image of each gallery on main page
   - UPDATED: Default CSS stylesheet changed (with display fix)
   - NEW: Slide show abilities added
   - FIXED: Footer showing on main page

Idut Gallery 1.0 (beta) - 20 December 2005
   - Initial Release


5. LICENSE
----------
Idut Gallery 2.1 beta
Copyright (c) 2005-2008 Idut (http://www.idut.co.uk)

This program is free for both personal and commercial and uses. You may
modify this program for your own use only. Please leave the "Powered by
Idut Gallery" link at the bottom of the gallery.

You may redistribute the original program freely. The distribution must
not have been modified, this includes all copyright statements. You must
not charge a fee for redistributions.

This program is distributed in the hope that it will be useful, but
without any warranty. Idut accept no responsibility for any loss or
damge incurred from the use of this software. Use at your own risk.

If you have any queries over the use of this software, direct them to
Idut via www.idut.co.uk. This software must not be installed or used
if you do not agree with the above.
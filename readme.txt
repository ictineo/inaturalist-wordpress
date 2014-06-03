/**
 * Plugin Name: iNaturalist
 * Plugin URI: http://www.inaturalist.org
 * Description: This plugin connects your wordpress to inaturalist platform 
 * Version: 1
 * Author: Julià Mestieri for Projecte Ictineo SCCL (http://projecteictineo.com) 
 * Author URI: http://projecteictineo.com
 * License: aGPLv3
 */
DESCRIPTION
===========

This module allows you to connect your Drupal 7 site with iNaturalist (www.inaturalist.org). You will be available to see iNaturalist observations, projects, places and taxos. Also this module suports authentication to iNaturalist and user and observation creation.

The iNaturalist module allows to show content as list, full page and map display. Also this ones are available as main content element and as block.

iNaturalist has 2 submodules, one to work with observations data, and other for login and put data to www.inaturalist.org.

This module is also configurable to work with diferent iNaturalist forks and to limit data show on Drupal site to an user or project.


INSTALLATION
============
1- Download iNaturalist plugin tarball (.tar.gz) file.
2- Untar it into: wp-content/plugins/
3- Enable it in your dashboard: Plugins->Installed Plugin->Activate iNaturalist
4- Create new page with title: 'inat'
5- Visit it to register the page id. This will be done automaticaly, but is needed to visit it.
6- Configure your iNaturalist plugin in your Dashboard: Settings->iNaturalist. iNaturalist base url is requiered. All login options are requierd for login widget and add user and observation functions.
7- Enable widgets you want. iNaturalist menu and iNaturalist login are aviable for plugin v1
8- Enjoy it!


TODO
====
Include translate files for strings
Develop more widgets functionality
Add options to login widget

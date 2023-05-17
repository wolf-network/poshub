/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	// Instruct CKEditor to disable the filter. You can also limit this to specific tags.

	config.allowedContent = true;
	config.extraAllowedContent = '*[id];*(*);*{*};p(*)[*]{*};div(*)[*]{*};li(*)[*]{*};ul(*)[*]{*};span(*)[*]{*};table(*)[*]{*};td(*)[*]{*};fieldset(*)[*]{*}';
    config.removeButtons = 'Source,Save';

	// config.allowedContent = {
 //        script: true,
 //        $1: {
 //            // This will set the default set of elements
 //            elements: CKEDITOR.dtd,
 //            attributes: true,
 //            styles: true,
 //            classes: true
 //        }
 //    };
};
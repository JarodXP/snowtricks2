const $ = require('jquery');

import {UploadImageDisplayer} from "./UploadImageDisplayer";

//Defines the type of the thumbnail element (img, video or embed)
let thumbnailType = $('#thumbnailMedia').prop('tagName').toLowerCase();
let fileInputId = '';
let embed = null;

//Sets the variables depending on the type
switch (thumbnailType) {
    case 'img': fileInputId = '#trick_media_form_image';
    break;

    case 'video': fileInputId = '#trick_media_form_video';
    break;

    case 'div':
        fileInputId = '#embed_media_form_htmlCode';
        embed = true;
    break;

    default: break;
}

UploadImageDisplayer.initialize(fileInputId, '#thumbnailMedia', embed);
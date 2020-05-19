const $ = require('jquery');

import {UploadImageDisplayer} from "./UploadImageDisplayer";

//Defines the type of the thumbnail element (img, video or embed)
let form = $('form');
let mediaType;
let inputId;

if (form.attr('name') !== 'trick_media_form'){
    inputId = '#embed_media_form_htmlCode';
    mediaType = 'embed';
}
else if(form.find('input').attr('name') === 'trick_media_form[image]'){
    inputId = '#trick_media_form_image';
    mediaType = 'image';
}
else {
    inputId = '#trick_media_form_video';
    mediaType = 'video';
}

UploadImageDisplayer.initialize(inputId, '#thumbnailMedia', mediaType);
const $ = require('jquery');

//Object to display the image corresponding to the file to be uploaded
const UploadImageDisplayer = {
    fileInput: '',
    fileLabel: '',
    imageElement: '',

    initialize(fileInputId, mediaId = null){

        //Sets the element to be used in the callback function
        this.fileInput =  $(fileInputId);
        this.fileLabel = $(fileInputId + '+ label');

        //Checks if imageId is null before setting the element
        if(mediaId){
            this.imageElement = $(mediaId);
            this.setFileSelectedHandler(false);
        }else {
            this.setFileSelectedHandler(true);
        }
    },

    setFileSelectedHandler(fileNameOnly){

        if(fileNameOnly){
            this.fileInput.on('change', this.displayFileName);
        }else {
            this.fileInput.on('change', this.displayImageAndName);
        }
    },

    displayImageAndName(){
        if(UploadImageDisplayer.fileInput.prop('files') && UploadImageDisplayer.fileInput.prop('files')[0]){
            const uploadedFile = UploadImageDisplayer.fileInput.prop('files')[0];

            //Uses FileReader to read the image
            const reader = new FileReader();

            //Set an event handler when FileReader is ready
            reader.onload = function (e) {

                //Sets the image element src attribute
                UploadImageDisplayer.imageElement.attr('src', e.target.result);

                //Sets the label above input element
                UploadImageDisplayer.fileLabel.text(uploadedFile.name);
            };

            //Reader gets the file and returns a url
            reader.readAsDataURL(uploadedFile);
        }
    },

    //Sets only the fileName on the label
    displayFileName(){

        if(UploadImageDisplayer.fileInput.prop('files') && UploadImageDisplayer.fileInput.prop('files')[0]){
            const uploadedFile = UploadImageDisplayer.fileInput.prop('files')[0];

            //Sets the label above input element
            UploadImageDisplayer.fileLabel.text(uploadedFile.name);
        }
    }
};

export {UploadImageDisplayer};
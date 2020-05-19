const $ = require('jquery');

//Object to display the image corresponding to the file to be uploaded
const UploadImageDisplayer = {
    fileInput: '',
    fileLabel: '',
    imageElement: '',

    initialize(fileInputId, mediaId = null, embed = null){

        //Sets the element to be used in the callback function
        this.fileInput =  $(fileInputId);
        this.fileLabel = $(fileInputId + '+ label');

        //Checks if imageId is null before setting the element
        if(mediaId){
            this.imageElement = $(mediaId);
            this.setFileSelectedHandler(false, embed);
        }else {
            this.setFileSelectedHandler(true);
        }
    },

    setFileSelectedHandler(fileNameOnly, embed = null){

        //Sets the objects to send as parameters to the closures
        const fileInput = this.fileInput;
        const fileLabel = this.fileLabel;
        const imageElement = this.imageElement;

        //Sets the right event listener depending on the fileNameOnly parameter
        if(fileNameOnly){
            this.fileInput.on('change', function (){
                UploadImageDisplayer.displayFileName(fileInput, fileLabel)
            });
        }else if(embed == null) {
            this.fileInput.on('change', function (){
                UploadImageDisplayer.displayImageAndName(fileInput, fileLabel, imageElement)
            });
        }else {
            this.fileInput.on('change', function (){
                UploadImageDisplayer.displayEmbed(fileInput, imageElement)
            });
        }
    },

    displayImageAndName(fileInput, fileLabel, imageElement){
        if(fileInput.prop('files') && fileInput.prop('files')[0]){
            const uploadedFile = fileInput.prop('files')[0];

            //Uses FileReader to read the image
            const reader = new FileReader();

            //Set an event handler when FileReader is ready
            reader.onload = function (e) {

                //Sets the image element src attribute
                imageElement.attr('src', e.target.result);

                //Sets the label above input element
                fileLabel.text(uploadedFile.name);
            };

            //Reader gets the file and returns a url
            reader.readAsDataURL(uploadedFile);
        }
    },

    //Sets only the fileName on the label
    displayFileName(fileInput, fileLabel){

        if(fileInput.prop('files') && fileInput.prop('files')[0]){
            const uploadedFile = fileInput.prop('files')[0];

            //Sets the label above input element
            fileLabel.text(uploadedFile.name);
        }
    },

    //Copies the html code pasted by the user into the image element
    displayEmbed(fileInput, imageElement){
        let htmlCode = fileInput.val();
        imageElement.html(htmlCode);
    }
};

export {UploadImageDisplayer};
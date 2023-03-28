/**
 * nicUpload
 * @description: A button to allow users to upload images (hosted by imgur)
 * @requires: nicCore, nicPane, nicAdvancedButton
 * @author: Brian Kirchoff
 * @sponsored by: DotConcepts (http://www.dotconcepts.net)
 * @version: 0.9.0
 */

let nicUploadOptions = {
    buttons : {
        'upload': {name: 'Upload Image', type: 'nicUploadButton'}
    },
    iconFiles: {'upload': rootUrl + '/images/design/nicEdit/icons/nicUpload.gif'},
};

var nicUploadButton = nicEditorAdvancedButton.extend({	
    // nicURI : 'https://api.imgur.com/3/image',
    nicURI : rootUrl + '/teacher/contentImage',
    errorText : 'Failed to upload image',

    addPane: function() {
        if(typeof window.FormData === 'undefined') {
          return this.onError('Image uploads are not supported in this browser, use Chrome, Firefox, or Safari instead.');
        }
        this.im = this.ne.selectedInstance.selElm().parentTag('IMG');

        let container = new bkElement('div')
            .setStyle({ padding: '10px' })
            .appendTo(this.pane.pane);

        new bkElement('div')
            .setStyle({ fontSize: '14px', fontWeight : 'bold', paddingBottom: '5px' })
            .setContent('Insert an Image')
            .appendTo(container);

        this.fileInput = new bkElement('input')
            .setAttributes({ 'type' : 'file' })
            .appendTo(container);

        this.progress = new bkElement('progress')
            .setStyle({ width : '100%', display: 'none' })
            .setAttributes('max', 100)
            .appendTo(container);

        this.fileInput.onchange = this.uploadFile.closure(this);
    },

    onError: function(msg) {
        this.removePane();
        alert(msg || "Failed to upload image");
    },

    uploadFile: function() {
        let file = this.fileInput.files[0];
        if (! file || ! file.type.match(/image.*/)) {
            this.onError('Only image files can be uploaded');
            return;
        }
        
        this.fileInput.setStyle({ display: 'none' });
        this.setProgress(0);
        
        let fd = new FormData(); // https://hacks.mozilla.org/2011/01/how-to-develop-a-html5-image-uploader/
        fd.append('image', file);
        
        let xhr = new XMLHttpRequest();
        xhr.open("POST", this.ne.options.uploadURI || this.nicURI);
        
        xhr.onload = function() {
            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch(e) {
                return this.onError();
            }
            if (response.error || ! response.data) {
                return this.onError(response.error);
            } else {
                return this.onUploaded(response.data);
            }
        }.closure(this);
        
        xhr.onerror = this.onError.closure(this);
        xhr.upload.onprogress = function(e) {
            this.setProgress(e.loaded / e.total);
        }.closure(this);
        
        xhr.setRequestHeader('Authorization', 'Client-ID c37fc05199a05b7');
        xhr.send(fd);
    },
    
    setProgress: function(percent) {
        this.progress.setStyle({ display: 'block' });
        if(percent < .98) {
            this.progress.value = percent;
        } else {
            this.progress.removeAttribute('value');
        }
    },
    
    onUploaded: function(options) {
        this.removePane();
        let src = options.link;
        if(! this.im) {
            this.ne.selectedInstance.restoreRng();
            this.ne.nicCommand("insertImage", src);
            this.im = this.findElm('IMG','src', src);
        }
        let w = parseInt(this.ne.selectedInstance.elm.getStyle('width'));
        if (this.im) {
            this.im.setAttributes({
                src : src,
                width : (w && options.width) ? Math.min(w, options.width) : ''
            });
            this.im.className = 'nicContentImage';
        }
    }
});

nicEditors.registerPlugin(nicPlugin, nicUploadOptions);

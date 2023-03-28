let nicYoutubeOptions = {
    buttons : {
        'upload': {name: 'Youtube', type: 'nicYoutubeButton'}
    },
    iconFiles: {'upload': rootUrl + '/images/design/nicEdit/icons/nicYoutube.gif'},
};

var nicYoutubeButton = nicEditorAdvancedButton.extend({	
    width: '350px',
    
    addPane: function () {
        this.addForm({
            '': { type: 'title', txt: 'YouTube Url' },
            'youTubeUrl': { type: 'text', txt: 'URL', value: 'http://', style: { width: '150px'} },
            'height': { type: 'text', txt: 'Height', value: this.ne.customOptions.nicYoutube.width, style: { width: '150px'} },
            'width': { type: 'text', txt: 'Width', value: this.ne.customOptions.nicYoutube.height, style: { width: '150px'} }
        });
    },
    
    submit: function (e) {
        let code = this.inputs['youTubeUrl'].value;
        let width = this.inputs['height'].value;
        let height = this.inputs['width'].value;

        if (code.indexOf('watch?v=') > 0) {
            code = code.replace('watch?v=','embed/');
        }
        
        let youTubeCode = '<p><br></p><iframe class="nicYoutubeIframe" width="' + width + '" height="' + height + '" src="' + code + '" frameborder="0" allowfullscreen></iframe><p><br></p>';
        
        this.removePane();
        this.ne.nicCommand('insertHTML', youTubeCode);
    }
});

nicEditors.registerPlugin(nicPlugin, nicYoutubeOptions);

// This should be a global variable.
var nicHtmlCode = (function() {
    let options = {
        buttons: {
            'upload' : {name: 'Edit HTML', type: 'nicHtmlCode'},
        },
        iconFiles: {'upload': rootUrl + '/images/design/nicEdit/icons/nicHtmlCode.gif'},
    };

    nicEditors.registerPlugin(nicPlugin, options);
    
    let pluginButton = nicEditorAdvancedButton.extend({
        
        width: '800px',

        addPane: function (bkElm) {
            this.addForm({
              '': { type: 'title', txt: 'Insert Html' },
              'code' : {
                    type : 'content',
                    'value' : this.ne.selectedInstance.getContent(),
                    style : {width: '100%', height : '300px'}
                },
            });
        },
        
        submit: function(e) {
            this.removePane();
            this.ne.selectedInstance.setContent(this.inputs['code'].value);
        },

    });
    
    return pluginButton;
})();

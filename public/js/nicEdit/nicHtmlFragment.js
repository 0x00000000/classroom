// This should be a global variable.
var nicHtmlFragment = (function() {
    let options = {
        buttons: {
            upload: {
                name: 'HTML fragment',
                type: 'nicHtmlFragment',
            },
        },
        iconFiles: {
            upload: rootUrl + '/images/design/nicEdit/icons/nicHtmlFragment.gif',
        },
    };

    nicEditors.registerPlugin(nicPlugin, options);
    
    let pluginButton = nicEditorAdvancedButton.extend({
        
        fragmentClassName: 'nicMyhtmlFragment',

        fragmentElement: null,
        
        width: '800px',

        addPane: function (bkElm) {
            let selected = this.ne.selectedInstance.selElm().parentTag('DIV');
            let fragmentElement = null;
            if (selected.closest) {
                this.fragmentElement = selected.closest('.' + this.fragmentClassName)
            } else {
                this.fragmentElement = null;
            }
            
            this.addForm({
                '': {
                    type: 'title',
                    txt: 'Insert Html',
                },
                code: {
                    type: 'content',
                    value: this.fragmentElement ? this.fragmentElement.innerHTML : '',
                    style: {width: '100%', height : '300px'},
                },
            });
        },
        
        submit: function(e) {
            let mycode = this.inputs['code'].value;
            this.removePane();
            if (this.fragmentElement) {
                this.fragmentElement.innerHTML = mycode
            } else {
                this.ne.nicCommand(
                    'insertHTML',
                    '<div class="' + this.fragmentClassName + '">' + mycode + '</div>'
                );
            }
        },

    });
    
    return pluginButton;
})();

// This should be a global variable.
var nicTable = (function() {
    let options = {
        buttons: {
            upload: {
                name: 'Insert table',
                type: 'nicTable',
            },
        },
        iconFiles: {
            upload: rootUrl + '/images/design/nicEdit/icons/nicTable.gif',
        },
    };

    nicEditors.registerPlugin(nicPlugin, options);
    
    let pluginButton = nicEditorAdvancedButton.extend({
        
        fragmentClassName: 'nicMyhtmlFragment',
        
        getTableContent: function(rows, cols) {
            console.log(rows, cols);
            let html = '';
            
            rows = Number(rows);
            cols = Number(cols);
            if (rows <= 0 || cols <= 0) {
                return html;
            }
            
            html += '<table class="nicTable" cellspacing="0" cellpadding="0"><tbody>';
            for (let i = 0; i < rows; i++) {
                html += '<tr>';
                for (let j = 0; j < cols; j++) {
                    html += '<td>&nbsp;</td>';
                }
                html += '</tr>';
            }
            html += '</tbody></table>';
            console.log(html);
            return html;
        },
    
        addPane: function (bkElm) {
            this.addForm({
                '': {
                    type: 'title',
                    txt: 'Insert table',
                },
                rows : {
                      type: 'text',
                      txt: 'Rows',
                      value: '3',
                      style: {width: '150px'},
                },
                cols : {
                    type: 'text',
                    txt: 'Cols',
                    value: '3',
                    style: {width: '150px'},
                },
            });
        },
        
        submit: function(e) {
            this.removePane();
            let html = '<br><div class="' + this.fragmentClassName + '">'
                + this.getTableContent(this.inputs['rows'].value, this.inputs['cols'].value)
                + '</div><br>';
            this.ne.nicCommand(
                'insertHTML',
                html
            );
        },

    });
    
    return pluginButton;
})();

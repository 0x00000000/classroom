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
        
        getTableContent: function(rows, cols, fixedWidth) {
            let html = '';
            
            if (typeof fixedWidth === undefined) {
                fixedWidth = false;
            }
            
            rows = Number(rows);
            cols = Number(cols);
            if (rows <= 0 || cols <= 0) {
                return html;
            }
            
            let widthAttr = '';
            if (fixedWidth) {
                widthAttr = ' width="' + Math.floor(100 / cols) + '%"'
            }
            
            html += '<table class="nicTable" cellspacing="0" cellpadding="0"><tbody>';
            for (let i = 0; i < rows; i++) {
                html += '<tr>';
                for (let j = 0; j < cols; j++) {
                    html += '<td' + widthAttr + '></td>';
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
                      value: nicTable.rows,
                      style: {width: '150px'},
                },
                cols : {
                    type: 'text',
                    txt: 'Cols',
                    value: nicTable.cols,
                    style: {width: '150px'},
                },
                width : {
                    type: 'select',
                    txt: 'Width',
                    value: nicTable.width,
                    options: {
                        'auto': 'Auto',
                        'fixed': 'Fixed',
                    }
                },
            });
        },
        
        submit: function(e) {
            this.removePane();
            nicTable.rows = this.inputs['rows'].value;
            nicTable.cols = this.inputs['cols'].value;
            nicTable.width = this.inputs['width'].value;
            let fixedWidth = nicTable.width === 'fixed';
            let html = '<br><div class="' + this.fragmentClassName + '">'
                + this.getTableContent(nicTable.rows, nicTable.cols, fixedWidth)
                + '</div><br>';
            this.ne.nicCommand(
                'insertHTML',
                html
            );
        },

    });
    
    return pluginButton;
})();

// We will store last values in these properties.
nicTable.rows = '3';
nicTable.cols = '3';
nicTable.width = 'auto';

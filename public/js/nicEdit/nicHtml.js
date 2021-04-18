var nicMyhtmlOptions = {
    buttons : {
      'upload' : {name : 'Insert Html', type : 'nicMyhtmlButton'},
    },
    iconFiles : {'upload' : rootUrl + '/images/design/nicEdit/icons/nicHtml.gif'}
};

var nicMyhtmlButton = nicEditorAdvancedButton.extend({
    addPane: function () {
      this.addForm({
        '': { type: 'title', txt: 'Insert Html' },
        'code' : {type : 'content', 'value' : '', style : {width: '340px', height : '200px'}}
      });
    },
    
    submit: function(e) {
      var mycode = this.inputs['code'].value;
      this.removePane();
      this.ne.nicCommand('insertHTML', mycode);
    }

});

nicEditors.registerPlugin(nicPlugin, nicMyhtmlOptions);

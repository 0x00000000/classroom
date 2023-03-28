function initNicEdit(params) {
    bkLib.onDomLoaded(function() {
        var iconsPath = (typeof rootUrl !== 'undefined' ? rootUrl : '')
            + '/vendor/nicEdit/nicEditorIcons.gif';
        var nicEditParams = {
            maxHeight : 400,
            iconsPath: iconsPath,
        };
        if (params.emptyPanel) {
            nicEditParams.buttonList = [];
        } else if (params.simplePanel) {
            nicEditParams.buttonList = ['bold','italic','underline','forecolor'];
        }
        var nicEdit = new nicEditor(nicEditParams).panelInstance(params.panelId);
        var htmlContainer = null;
        var panelContainer = document.getElementById(params.panelContainerId);
        if (panelContainer) {
            htmlContainer = panelContainer.querySelector('.nicEdit-main');
        }

        if (params.inputId) {
            var input = document.getElementById(params.inputId);
            if (input && htmlContainer) {
                nicEdit.addEvent('blur', function() {
                    input.value = htmlContainer.innerHTML;
                });
            }
        }

        nicEdit.customOptions = params.customOptions;

        if (typeof params.onInit === 'function') {
            params.onInit({nicEdit: nicEdit});
        }
    });
}

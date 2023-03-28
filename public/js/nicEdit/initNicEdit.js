function initNicEdit(params) {
    bkLib.onDomLoaded(function() {
        let iconsPath = (typeof rootUrl !== 'undefined' ? rootUrl : '')
            + '/vendor/nicEdit/nicEditorIcons.gif';
        let nicEditParams = {
            maxHeight : 400,
            iconsPath: iconsPath,
        };
        if (params.emptyPanel) {
            nicEditParams.buttonList = [];
        } else if (params.simplePanel) {
            nicEditParams.buttonList = ['bold','italic','underline','forecolor'];
        }
        let nicEdit = new nicEditor(nicEditParams).panelInstance(params.panelId);
        let htmlContainer = null;
        let panelContainer = document.getElementById(params.panelContainerId);
        if (panelContainer) {
            htmlContainer = panelContainer.querySelector('.nicEdit-main');
        }

        if (params.inputId) {
            let input = document.getElementById(params.inputId);
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

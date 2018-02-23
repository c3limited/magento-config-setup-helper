var ConfigContentForm = Class.create();
ConfigContentForm.prototype = {
    initialize: function () {
        this.dialogWindowId = 'aw-pq2-form-content';
    },
    open: function (text, parameters, title, callback, width) {
        if (this.dialogWindow) {
            this.closeDialogWindow(this.dialogWindow);
        }
        this.openDialogWindow(text, title, width, callback);
        var me = this;
        Event.observe(window, 'resize', function(){
            var height = window.document.documentElement.clientHeight;
            var width = window.document.documentElement.clientWidth;
            if (me.dialogWindow.width <= width && me.dialogWindow.height <= height){
                me.dialogWindow.showCenter();
            }
        });
    },
    openDialogWindow: function (content, title, width, callback) {
        if ($(this.dialogWindowId) && typeof(Windows) != 'undefined') {
            Windows.focus(this.dialogWindowId);
            return;
        }
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration: 0};
        Windows.overlayHideEffectOptions = {duration: 0};
        var me = this;
        this.dialogWindow = Dialog.info(content, {
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            windowClassName: "popup-window",
            title: title,
            width: width,
            //height:270,
            zIndex: 104,
            recenterAuto: false,
            hideEffect: Element.hide,
            showEffect: Element.show,
            id: this.dialogWindowId,
            onClose: function() {
                callback(me);
                me.closeDialogWindow.bind(me);
            },
            top: 200,
            left: (window.document.documentElement.clientWidth - width) / 2
        });

        content.evalScripts.bind(content).defer();
    },
    closeDialogWindow: function (window) {
        if (!window) {
            window = this.dialogWindow;
        }
        if (window) {
            window.close();
            Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
            Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
        }
    }
};
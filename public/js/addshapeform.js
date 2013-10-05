Ext.namespace('addShape');
addShape.init = function () {
    "use strict";
    Ext.QuickTips.init();
    var msg = function (title, msg) {
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };
    addShape.form = new Ext.FormPanel({
        region: 'center',
        id: "addform",
        fileUpload: true,
        frame: false,
        border: false,
        title: 'ESRI Shape file upload',
        autoHeight: true,
        bodyStyle: 'padding: 10px 10px 0 10px',
        labelWidth: 60,
        defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'textfield',
                name: 'name',
                emptyText: 'Name of table',
                allowBlank: false
            },
            {
                xtype: 'numberfield',
                name: 'srid',
                emptyText: 'Choose EPSG number'
            },
            {
                xtype: 'fileuploadfield',
                id: 'form-shp',
                emptyText: 'Select .shp',
                //fieldLabel: 'Shp',
                name: 'shp',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            },
            {
                xtype: 'fileuploadfield',
                id: 'form-dbf',
                emptyText: 'Select .dbf',
                //fieldLabel: 'Dbf',
                name: 'dbf',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            },
            {
                xtype: 'fileuploadfield',
                id: 'form-shx',
                emptyText: 'Select .shx',
                //fieldLabel: 'Shx',
                name: 'shx',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            },
            {
                xtype: 'checkbox',
                name: 'pdo',
                fieldLabel: 'Direct load'
            }
        ],
        buttons: [
            {
                text: 'Save',
                handler: function () {
                    if (addShape.form.getForm().isValid()) {
                        addShape.form.getForm().submit({
                            url: '/controllers/upload/shape',
                            //waitMsg: 'Uploading your shape file...',
                            success: addShape.onSubmit,
                            failure: addShape.onSubmit
                        });
                    }
                }
            },
            {
                text: 'Reset',
                handler: function () {
                    addShape.form.getForm().reset();
                }
            }
        ]
    });
};
addShape.onSubmit = function (form, action) {
    "use strict";
    var result = action.result;
    if (result.success) {
        store.load();
        App.setAlert(App.STATUS_NOTICE, result.message);
        //addShape.form.reset();
    } else {
        Ext.MessageBox.alert('Failure', result.message);
    }
}

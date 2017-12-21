Ext.ux.Image = Ext.extend(Ext.Component, {
    url  : '',  //for initial src value

    autoEl: {
        tag: 'img',
        src: Ext.BLANK_IMAGE_URL,
        cls: 'tng-managed-image',
        width: 100,
        height: 100
    },
    onRender: function() {
        Ext.ux.Image.superclass.onRender.apply(this, arguments);
        this.el.on('load', this.onLoad, this);
        if(this.url){
            this.setSrc(this.url);
        }
    },
    onLoad: function() {
        this.fireEvent('load', this);
    },
    setSrc: function(src) {
        if (src == '' || src == undefined) {
            this.el.dom.src = Ext.BLANK_IMAGE_URL;
            Ext.getCmp('currimg').hide();
        } else {
            this.el.dom.src = MODx.config.base_url + src;
            Ext.getCmp('currimg').show();
        }
    }
});
Ext.reg('image', Ext.ux.Image);

Bendera.grid.Items = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'bendera-grid-items'
        ,url: Bendera.config.connector_url
        ,baseParams: {
            action: 'mgr/item/getlist'
            ,context: config.id
        }
        ,fields: ['id', 'title', 'description', 'content', 'chunk', 'html', 'image', 'image_newimage',  'size', 'type', 'context', 'categories', 'startdate', 'enddate', 'link_internal', 'link_external', 'active']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 70
        },{
            header: _('title')
            ,dataIndex: 'title'
            ,width: 200
        },{
            header: 'Soort advertentie'
            ,dataIndex: 'type'
            ,width: 250
        }]
        ,tbar: [{
            text: 'Nieuwe advertentie'
            ,handler: this.createItem
            ,scope: this
        }]
    });
    Bendera.grid.Items.superclass.constructor.call(this,config);
};

Ext.extend(Bendera.grid.Items, MODx.grid.Grid, {
    windows: {}
    ,getMenu: function() {
        var m = [];
        m.push({
                   text: 'Advertentie aanpassen'
                   ,handler: this.updateItem
               });
        m.push('-');
        m.push({
                   text: 'Advertentie verwijderen'
                   ,handler: this.removeItem
               });
        this.addContextMenuItem(m);
    }
    ,createItem: function(btn,e) {
        if (this.windows.createItem) {
            this.windows.createItem.destroy();
        }
        if (this.windows.updateItem) {
            this.windows.updateItem.close();
        }
        this.windows.createItem = MODx.load({
            xtype: 'bendera-window-item-create'
            ,context: this.config.id
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        this.windows.createItem.fp.getForm().reset();
        this.windows.createItem.show(e.target);
        Ext.getCmp('kind-of-ad').setValue('image');
    }
    ,updateItem: function(btn, e) {
        if (!this.menu.record || !this.menu.record.id) return false;
        var r = this.menu.record;
        if (this.windows.updateItem) {
            this.windows.updateItem.destroy();
        }
        if (this.windows.createItem) {
            this.windows.createItem.close();
        }
        this.windows.updateItem = MODx.load({
            xtype: 'bendera-window-item-update'
            ,context: this.config.id
            ,record: r
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        this.windows.updateItem.fp.getForm().reset();
        this.windows.updateItem.fp.getForm().setValues(r);

        Ext.getCmp('categories-box').setValue(r.categories);
        this.windows.updateItem.show(e.target);
        Ext.getCmp('currimg').setSrc(this.menu.record.image);
    }
    ,removeItem: function(btn, e) {
        if (!this.menu.record) {
            return false;
        }

        MODx.msg.confirm({
            title: 'Advertentie verwijderen'
            ,text: 'Weet u zeker dat u deze advertentie wilt verwijderen?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {
                    fn:function(r) {
                        this.refresh();
                    },
                    scope:this
                }
            }
        });
    }
});
Ext.reg('bendera-grid-items', Bendera.grid.Items);

Bendera.window.CreateItem = function(config) {
    config = config || {};

    this.ident = config.ident || Ext.id();
    Ext.applyIf(config,{
        title: 'Nieuwe advertentie'
        ,id: this.ident
        ,layout:'form'
        ,bodyStyle: 'padding: 10px'
        ,autoScroll: true
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,width: 400
        ,modal: true
        ,labelWidth: 150
        ,url: Bendera.config.connector_url
        ,baseParams: {
            action: 'mgr/item/create'
            ,context: config.context
        }
        ,fields: [{
            xtype: 'modx-combo'
            ,fieldLabel: 'Soort advertentie'
            ,name: 'type'
            ,hiddenName: 'type'
            ,id: 'kind-of-ad'
            ,width: 300
            ,allowBlank: false
            ,emptyValue: 'image'
            ,mode: 'local'
            ,store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'value',
                    'display'
                ],
                data: Bendera.config.types
            })
            ,listeners: {
                'afterrender': {
                    fn: function() {
                        Bendera.SetVisibleFields('image');
                    }
                    ,scope:this
                }
            }
            ,valueField: 'value'
            ,displayField: 'display'
        },{
            xtype: (MODx.config['bendera.use_dates'] == 1) ? 'xdatetime' : 'hidden'
            ,fieldLabel: _('bendera.startdate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'startdate'
            ,width: 300
        },{
            xtype: (MODx.config['bendera.use_dates'] == 1) ? 'xdatetime' : 'hidden'
            ,fieldLabel: _('bendera.enddate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'enddate'
            ,width: 300
        },{
            allowBlank:false,
            id:'categories-box',
            xtype: (MODx.config['bendera.use_categories'] == 1) ? 'superboxselect' : 'hidden',
            fieldLabel: 'Advertentie plaatsen in de categorieën',
            emptyText: _('bendera.resourceselect'),
            resizable: true,
            minChars: 2,
            name: 'categories',
            hiddenName: 'categories[]',
            store: Bendera.templateList['templates'],
            context:config.context,
            mode: 'remote',
            displayField: 'text',
            valueField: 'value',
            queryDelay: 0,
            triggerAction: 'all',
            stackItems: true,
            width:300
            ,extraItemCls: 'x-tag'
            ,expandBtnCls: 'x-form-trigger'
            ,clearBtnCls: 'x-form-trigger'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('title')
            ,name: 'title'
            ,id: 'title'
            ,allowBlank: false
            ,width: 300
        },{
            xtype: 'modx-combo'
            ,url: Bendera.config.connector_url
            ,fields: ['id', 'name']
            ,hiddenName: 'chunk'
            ,displayField: 'name'
            ,valueField: 'id'
            ,baseParams: {
                action: 'mgr/chunk/getlist'
                ,limit: 20
                ,sort: 'name'
                ,dir: 'asc'
            }
            ,fieldLabel: _('bendera.type.chunk')
            ,id: 'chunk'
            ,name: 'chunk'
            ,paging: true
            ,pageSize: 20
            ,typeAhead: true
            ,editable: true
            ,forceSelection: true
            ,width: 300
        },{
            xtype: 'modx-combo'
            ,url: Bendera.config.connector_url
            ,fields: ['id', 'pagetitle']
            ,hiddenName: 'link_internal'
            ,displayField: 'pagetitle'
            ,valueField: 'id'
            ,baseParams: {
                action: 'mgr/resource/getlist'
                ,limit: 20
                ,sort: 'pagetitle'
                ,dir: 'asc'
            }
            ,fieldLabel: _('bendera.link_internal')
            ,id: 'link_internal'
            ,name: 'link_internal'
            ,paging: true
            ,pageSize: 20
            ,typeAhead: true
            ,editable: true
            ,forceSelection: true
            ,width: 300
        }, {
            xtype: 'textfield'
            ,fieldLabel: _('bendera.link_external')
            ,name: 'link_external'
            ,id: 'link_external'
            ,width: 300
        },{
            xtype: 'label'
            ,id: 'link_external_help'
            ,text: _('bendera.link_external-help')
            ,cls: 'desc-under'
        },{
            xtype: 'textarea'
            ,fieldLabel: 'Beschrijving'
            ,name: 'description'
            ,id: 'description'
            // ,allowBlank: false
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: 'HTML content'
            ,name: 'html'
            ,id: 'html'
            ,width: 300
            ,height: 220
        },{
            xtype: 'hidden'
            ,id: 'image'
        },{
            id: 'currimg'
            ,fieldLabel: 'Afbeelding'
            ,xtype: 'image'
        },{
            xtype: 'modx-combo-browser'
            ,name: 'image_newimage'
            ,id: 'image_newimage'
            ,allowedFileTypes: 'jpeg,gif,png,JPG,jpg'
            ,source: 2
            ,width: 300
            ,listeners: {
                'select': {
                    fn:function(data) {
                        var imageUrl = data.fullRelativeUrl;
                        if (imageUrl.indexOf('/') === 0){
                            imageUrl = imageUrl.replace('/','');
                        }
                        Ext.getCmp('currimg').setSrc(imageUrl);
                        Ext.getCmp('image').setValue(imageUrl);
                    }
                }
            }
        }, {
            xtype: 'checkbox'
            ,fieldLabel: _('bendera.active')
            ,name: 'active'
            ,id: 'active'
            ,inputValue: 1
            ,uncheckedValue: 0
        }]
    });
    Bendera.window.CreateItem.superclass.constructor.call(this,config);
    var BenderaKind = Ext.getCmp('kind-of-ad');
    if (BenderaKind) {
        BenderaKind.on('select',this.showFields);
    }

    if (BenderaKind.getValue() == '') {
        BenderaKind.setValue('image');
        Ext.getCmp('html').hide();
    }
};

Ext.extend(Bendera.window.CreateItem, MODx.Window, {
    showFields: function(cb) {
        Bendera.SetVisibleFields(cb.getValue());
    }
});
Ext.reg('bendera-window-item-create', Bendera.window.CreateItem);

Bendera.window.UpdateItem = function(config) {
    var templateList = new Ext.data.JsonStore({
        url: Bendera.config.connector_url
        ,baseParams:{
            action: 'mgr/item/templatelist'
        }
        ,fields: ['value','text']
    });

    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('bendera.item_update')
        ,id: this.ident
        ,layout:'form'
        ,bodyStyle: 'padding: 10px'
        ,autoScroll: true
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,width: 400
        ,modal: true
        ,labelWidth: 150
        ,url: Bendera.config.connector_url
        ,baseParams: {
            action: 'mgr/item/update'
            ,context: config.context
        }
        ,fields: [{
            xtype: 'modx-combo'
            ,fieldLabel: 'Soort advertentie'
            ,name: 'type'
            ,hiddenName: 'type'
            ,id: 'kind-of-ad'
            ,width: 300
            ,allowBlank: false
            ,emptyValue: 'image'
            ,mode: 'local'
            ,store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'value',
                    'display'
                ],
                data: Bendera.config.types
            })
            ,valueField: 'value'
            ,displayField: 'display'
        },{
            xtype:'hidden'
            ,name: 'id'
        },{
            xtype:'hidden'
            ,name: 'context'
        },{
            xtype: (MODx.config['bendera.use_dates'] == 1) ? 'xdatetime' : 'hidden'
            ,fieldLabel: _('bendera.startdate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'startdate'
            ,width: 300
        },{
            xtype: (MODx.config['bendera.use_dates'] == 1) ? 'xdatetime' : 'hidden'
            ,fieldLabel: _('bendera.enddate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'enddate'
            ,width: 300
        },{
            allowBlank:false,
            id:'categories-box',
            xtype: (MODx.config['bendera.use_categories'] == 1) ? 'superboxselect' : 'hidden',
            fieldLabel: 'Advertentie plaatsen in de categorieën',
            emptyText: _('bendera.resourceselect'),
            resizable: true,
            minChars: 2,
            name: 'categories',
            hiddenName: 'categories[]',
            store: Bendera.templateList['templates'],
            context:config.context,
            mode: 'remote',
            displayField: 'text',
            valueField: 'value',
            queryDelay: 0,
            triggerAction: 'all',
            stackItems: true,
            width:300
            ,extraItemCls: 'x-tag'
            ,expandBtnCls: 'x-form-trigger'
            ,clearBtnCls: 'x-form-trigger'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('title')
            ,name: 'title'
            ,id: 'title'
            ,allowBlank: false
            ,width: 300
        },{
            xtype: 'modx-combo'
            ,url: Bendera.config.connector_url
            ,fields: ['id', 'name']
            ,hiddenName: 'chunk'
            ,displayField: 'name'
            ,valueField: 'id'
            ,baseParams: {
                action: 'mgr/chunk/getlist'
                ,limit: 20
                ,value: config.record.chunk
                ,sort: 'name'
                ,dir: 'asc'
            }
            ,fieldLabel: _('bendera.type.chunk')
            ,id: 'chunk'
            ,name: 'chunk'
            ,paging: true
            ,pageSize: 20
            ,typeAhead: true
            ,editable: true
            ,forceSelection: true
            ,width: 300
        },{
            xtype: 'modx-combo'
            ,url: Bendera.config.connector_url
            ,fields: ['id', 'pagetitle']
            ,hiddenName: 'link_internal'
            ,displayField: 'pagetitle'
            ,valueField: 'id'
            ,baseParams: {
                action: 'mgr/resource/getlist'
                ,limit: 20
                ,value: config.record.link_internal
                ,sort: 'pagetitle'
                ,dir: 'asc'
            }
            ,fieldLabel: _('bendera.link_internal')
            ,id: 'link_internal'
            ,name: 'link_internal'
            ,paging: true
            ,pageSize: 20
            ,typeAhead: true
            ,editable: true
            ,forceSelection: true
            ,width: 300
            ,listeners: {
                'select': {
                    fn:function(data) {
                       console.log(this.value);
                    }
                }
            }
        }, {
            xtype: 'textfield'
            ,fieldLabel: _('bendera.link_external')
            ,name: 'link_external'
            ,id: 'link_external'
            ,width: 300
        },{
            xtype: 'label'
            ,id: 'link_external_help'
            ,text: _('bendera.link_external-help')
            ,cls: 'desc-under'
        },{
            xtype: 'textarea'
            ,fieldLabel: 'Beschrijving'
            ,name: 'description'
            ,id: 'description'
            // ,allowBlank: false
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: 'HTML content'
            ,name: 'html'
            ,id: 'html'
            ,width: 300
            ,height: 220
        },{
            xtype: 'hidden'
            ,id: 'image'
        },{
            id: 'currimg'
            ,fieldLabel: 'Afbeelding'
            ,xtype: 'image'
        },{
            xtype: 'modx-combo-browser'
            ,name: 'image_newimage'
            ,id: 'image_newimage'
            ,allowedFileTypes: 'jpeg,gif,png,JPG,jpg'
            ,source: 2
            ,width: 300
            ,listeners: {
                'select': {
                    fn:function(data) {
                        var imageUrl = data.fullRelativeUrl;
                        if (imageUrl.indexOf('/') === 0){
                            imageUrl = imageUrl.replace('/','');
                        }

                        Ext.getCmp('currimg').setSrc(imageUrl);
                        Ext.getCmp('image').setValue(imageUrl);
                    }
                }
            }
        }, {
            xtype: 'checkbox'
            ,fieldLabel: _('bendera.active')
            ,name: 'active'
            ,id: 'active'
            ,inputValue: 1
            ,uncheckedValue: 0
        }]
    });
    Bendera.window.UpdateItem.superclass.constructor.call(this, config);

    var BenderaKind = Ext.getCmp('kind-of-ad');
    if (BenderaKind) {
        BenderaKind.on('select', this.showFields);
    }

    if (BenderaKind.getValue() != '') {
        Bendera.SetVisibleFields(BenderaKind.getValue());
    }
};

Ext.extend(Bendera.window.UpdateItem, MODx.Window, {
    showFields: function(cb) {
        Bendera.SetVisibleFields(cb.getValue());
    }
});

Ext.reg('bendera-window-item-update', Bendera.window.UpdateItem);

Bendera.SetVisibleFields = function(value) {
    var titleField            = Ext.getCmp('title'),
        chunkField            = Ext.getCmp('chunk'),
        descriptionField      = Ext.getCmp('description'),
        linkInternalField     = Ext.getCmp('link_internal'),
        linkExternalField     = Ext.getCmp('link_external'),
        linkExternalFieldHelp = Ext.getCmp('link_external_help'),
        htmlField             = Ext.getCmp('html'),
        imageField            = Ext.getCmp('image'),
        currimgField          = Ext.getCmp('currimg'),
        newimgField           = Ext.getCmp('image_newimage');

    switch (value) {
        case 'banner':
            titleField.show();
            chunkField.hide();
            descriptionField.hide();
            htmlField.hide();
            imageField.show();
            currimgField.show();
            newimgField.show();
            linkInternalField.show();
            linkExternalField.show();
            linkExternalFieldHelp.show();
            break;
        case 'button':
            titleField.show();
            chunkField.hide();
            descriptionField.hide();
            htmlField.hide();
            imageField.hide();
            currimgField.hide();
            newimgField.hide();
            linkInternalField.show();
            linkExternalField.show();
            linkExternalFieldHelp.show();
            break;
        case 'chunk':
            titleField.show();
            chunkField.show();
            descriptionField.hide();
            htmlField.hide();
            imageField.hide();
            currimgField.hide();
            newimgField.hide();
            linkInternalField.hide();
            linkExternalField.hide();
            linkExternalFieldHelp.hide();
            break;
        case 'html':
            titleField.show();
            chunkField.hide();
            descriptionField.hide();
            htmlField.show();
            imageField.hide();
            currimgField.hide();
            newimgField.hide();
            linkInternalField.hide();
            linkExternalField.hide();
            linkExternalFieldHelp.hide();
            break;
        case 'image':
            titleField.show();
            chunkField.hide();
            descriptionField.show();
            htmlField.hide();
            imageField.show();
            currimgField.show();
            newimgField.show();
            linkInternalField.hide();
            linkExternalField.hide();
            linkExternalFieldHelp.hide();
            break;
        case 'affiliate':
            titleField.show();
            chunkField.hide();
            descriptionField.hide();
            htmlField.show();
            imageField.hide();
            currimgField.hide();
            newimgField.hide();
            linkInternalField.hide();
            linkExternalField.hide();
            linkExternalFieldHelp.hide();
            break;
    }
};
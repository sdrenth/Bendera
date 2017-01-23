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
        if(src == '' || src == undefined) {
            this.el.dom.src = Ext.BLANK_IMAGE_URL;
            Ext.getCmp('currimg').hide();
        }
        else {
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
        ,fields: ['id', 'title', 'description', 'content', 'image', 'flash_swf', 'image_newimage',  'size', 'type', 'context', 'categories', 'startdate', 'enddate', 'url']
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
Ext.extend(Bendera.grid.Items,MODx.grid.Grid,{
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
    ,updateItem: function(btn,e) {
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
        //console.log(Ext.getCmp('resource-box'));
        this.windows.updateItem.show(e.target);
        Ext.getCmp('currimg').setSrc(this.menu.record.image);
    }
    ,removeItem: function(btn,e) {
        if (!this.menu.record) return false;

        MODx.msg.confirm({
            title: 'Advertentie verwijderen'
            ,text: 'Weet u zeker dat u deze advertentie wilt verwijderen?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }
});
Ext.reg('bendera-grid-items',Bendera.grid.Items);


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
            ,id: 'kind-of-ad'
            ,width: 300
            ,allowBlank: false
            ,emptyValue: 'html'
            ,mode: 'local'
            ,store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'value',
                    'display'
                ],
                //data: [['html','HTML'],['flash','Flash'],['image','Afbeelding'],['affiliate','Affiliate']]
                data: [['image','Afbeelding'],['affiliate','Affiliate']]
            })
            ,valueField: 'value'
            ,displayField: 'display'
        },{
            xtype: 'xdatetime'
            ,fieldLabel: _('bendera.startdate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'startdate'
            ,width: 300
        },{
            xtype: 'xdatetime'
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
            xtype:'superboxselect',
            fieldLabel: 'Advertentie plaatsen in de categorieën',
            emptyText: _('bendera.resourceselect'),
            resizable: true,
            minChars: 2,
            name: 'categories',
            hiddenName: 'categories[]',
/*             anchor:'100%', */
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


/*
            xtype: 'superboxselect'
            ,fieldLabel: 'Advertentie plaatsen in de categorieën'
            ,name: 'resource[]'
            ,allowBlank:false
            ,store: resourceList
            ,forceSelection : true
            ,id:'resource-box'
            ,allowQueryAll : false
            ,displayField: 'text'
            ,typeAhead:true
            ,mode: 'remote'
            ,editable: true
            ,valueField: 'value'
            ,pageSize: 5
*/
        },{
            xtype: 'textfield'
            ,fieldLabel: 'Url'
            ,name: 'url'
            ,id: 'url'
            // ,allowBlank: false
            ,width: 300
        },{
            xtype: 'textfield'
            ,fieldLabel: _('title')
            ,name: 'title'
            ,id: 'title'
            ,allowBlank: false
            ,width: 300
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
            ,fieldLabel: 'Afbeelding (500px breed)'
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
        },{
            xtype: 'modx-combo-browser'
            ,name: 'flash_swf'
            ,id: 'flash_swf'
            ,width: 300
            ,fieldLabel: 'Flash bestand'
            ,allowedFileTypes: 'swf'
            ,source: 2
        }]

    });
    Bendera.window.CreateItem.superclass.constructor.call(this,config);
    var BenderaKind = Ext.getCmp('kind-of-ad');
    if (BenderaKind) { BenderaKind.on('select',this.showFields); }
    if(BenderaKind.getValue() == ''){
        BenderaKind.setValue('image');
        Ext.getCmp('html').hide();
        Ext.getCmp('flash_swf').hide();
    }
};
Ext.extend(Bendera.window.CreateItem,MODx.Window,{
    showFields: function(cb) {
        var cbValue = cb.getValue();
        var titleField = Ext.getCmp('title');
        var descriptionField = Ext.getCmp('description');
        var urlField = Ext.getCmp('url');
        var htmlField = Ext.getCmp('html');
        var imageField = Ext.getCmp('image');
        var currimgField = Ext.getCmp('currimg');
        var newimgField = Ext.getCmp('image_newimage');
        var flashField = Ext.getCmp('flash_swf');
        switch (cbValue) {
            case 'html':
                titleField.show();
                descriptionField.show();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();

            break;
            case 'image':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.show();
                currimgField.show();
                newimgField.show();
                flashField.hide();

            break;
            case 'flash':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.show();
            break;
            case 'affiliate':
                titleField.show();
                descriptionField.hide();
                urlField.hide();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();
            break;
        }
    }
});
Ext.reg('bendera-window-item-create',Bendera.window.CreateItem);


Bendera.window.UpdateItem = function(config) {
    // var resourceList = new Ext.data.JsonStore({
    //   url: Bendera.config.connector_url
    //   ,baseParams:{
    //     action: 'mgr/item/resourcelist'
    //     ,context: config.context
    //   }
    //   ,fields: ['value','text']
    // });

    var templateList = new Ext.data.JsonStore({
      url: Bendera.config.connector_url
      ,baseParams:{
        action: 'mgr/item/templatelist'
      }
      ,fields: ['value','text']
    });
    //console.log(config);
    //console.log(resourceList);

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
                //data: [['html','HTML'],['flash','Flash'],['image','Afbeelding'],['affiliate','Affiliate']]
                data: [['image','Afbeelding'],['affiliate','Affiliate']]
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
            xtype: 'xdatetime'
            ,fieldLabel: _('bendera.startdate')
            ,dateFormat: MODx.config.manager_date_format
            ,timeFormat: MODx.config.manager_time_format
            ,allowBlank: false
            ,selectOnFocus:true
            ,name: 'startdate'
            ,width: 300
        },{
            xtype: 'xdatetime'
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
            xtype:'superboxselect',
            fieldLabel: 'Advertentie plaatsen in de categorieën',
            emptyText: _('bendera.resourceselect'),
            resizable: true,
            minChars: 2,
            name: 'categories',
            hiddenName: 'categories[]',
/*             anchor:'100%', */
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
            xtype: 'textfield'
            ,fieldLabel: 'Url'
            ,name: 'url'
            ,id: 'url'
            // ,allowBlank: false
            ,width: 300
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
            ,name: 'content'
            ,id: 'html'
            ,width: 300
            ,height: 220
        },{
            xtype: 'hidden'
            ,id: 'image'
        },{
            id: 'currimg'
            ,fieldLabel: 'Afbeelding (500px breed)'
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
        },{
            xtype: 'modx-combo-browser'
            ,name: 'flash_swf'
            ,id: 'flash_swf'
            ,width: 300
            ,fieldLabel: 'Flash bestand'
            ,allowedFileTypes: 'swf'
            ,source: 2
        }]
    });
    Bendera.window.UpdateItem.superclass.constructor.call(this,config);
    //console.log(Ext.getCmp('resource-box'));

    var BenderaKind = Ext.getCmp('kind-of-ad');
    if (BenderaKind) { BenderaKind.on('select',this.showFields); }
    if(BenderaKind.getValue() != ''){
        var cbValue = BenderaKind.getValue();
        var titleField = Ext.getCmp('title');
        var descriptionField = Ext.getCmp('description');
        var urlField = Ext.getCmp('url');
        var htmlField = Ext.getCmp('html');
        var imageField = Ext.getCmp('image');
        var currimgField = Ext.getCmp('currimg');
        var newimgField = Ext.getCmp('image_newimage');
        var flashField = Ext.getCmp('flash_swf');
        switch (cbValue) {
            case 'html':
            case 'HTML':
                titleField.show();
                descriptionField.show();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();

            break;
            case 'image':
            case 'Image':
            case 'Afbeelding':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.show();
                currimgField.show();
                newimgField.show();
                flashField.hide();

            break;
            case 'flash':
            case 'Flash':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.show();
            break;
            case 'affiliate':
            case 'Affiliate':
                titleField.show();
                descriptionField.hide();
                urlField.hide();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();
            break;
        }
    }
};
Ext.extend(Bendera.window.UpdateItem,MODx.Window,{
    showFields: function(cb) {
        var cbValue = cb.getValue();
        var titleField = Ext.getCmp('title');
        var descriptionField = Ext.getCmp('description');
        var urlField = Ext.getCmp('url');
        var htmlField = Ext.getCmp('html');
        var imageField = Ext.getCmp('image');
        var currimgField = Ext.getCmp('currimg');
        var newimgField = Ext.getCmp('image_newimage');
        var flashField = Ext.getCmp('flash_swf');
        switch (cbValue) {
            case 'html':
                titleField.show();
                descriptionField.show();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();

            break;
            case 'image':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.show();
                currimgField.show();
                newimgField.show();
                flashField.hide();

            break;
            case 'flash':
                titleField.show();
                descriptionField.show();
                htmlField.hide();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.show();
            break;
            case 'affiliate':
                titleField.show();
                descriptionField.hide();
                urlField.hide();
                htmlField.show();
                imageField.hide();
                currimgField.hide();
                newimgField.hide();
                flashField.hide();
            break;
        }
    }
});
Ext.reg('bendera-window-item-update',Bendera.window.UpdateItem);
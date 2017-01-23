Bendera.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>Bendera</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('bendera.items')
                ,items: [{
                    html: '<p>' + _('bendera.menu_desc') + '</p><br />'
                    ,border: false
                },{
                    xtype: 'bendera-grid-items'
                    ,id: 'web'
                    ,preventRender: true
                }]
            }
        }]
    });
    Bendera.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Bendera.panel.Home,MODx.Panel);
Ext.reg('bendera-panel-home',Bendera.panel.Home);
//console.log(MODx);
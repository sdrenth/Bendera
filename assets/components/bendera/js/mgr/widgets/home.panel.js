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
            ,items: Bendera.contexts
        }]
    });
    Bendera.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Bendera.panel.Home,MODx.Panel);
Ext.reg('bendera-panel-home',Bendera.panel.Home);
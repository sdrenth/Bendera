Ext.onReady(function() {
    MODx.load({ xtype: 'bendera-page-home'});
});

Bendera.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'bendera-panel-home'
            ,renderTo: 'bendera-panel-home-div'
        }]
    });
    Bendera.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Bendera.page.Home,MODx.Component);
Ext.reg('bendera-page-home',Bendera.page.Home);
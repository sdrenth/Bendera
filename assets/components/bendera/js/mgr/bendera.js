var Bendera = function(config) {
    config = config || {};
    Bendera.superclass.constructor.call(this,config);
};
Ext.extend(Bendera,Ext.Component,{
    page:{}
    ,window:{}
    ,grid:{}
    ,tree:{}
    ,panel:{}
    ,combo:{}
    ,config: {}
    ,view: {}
});
Ext.reg('bendera', Bendera);

Bendera = new Bendera();
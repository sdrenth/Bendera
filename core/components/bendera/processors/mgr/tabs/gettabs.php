<?php

return 'test';

$array = array(
	array('id'=>"'<p>'+_('bendera.intro_msg')+'</p><br />'"),
	array('id'=>"'<p>'+_('bendera.intro_msg')+'</p><br />'"),
);

return json_decode($array);
/*
				[{
                    html:
                    ,border: false
                },{
                    xtype: 'bendera-grid-items'
                    ,preventRender: true
                }]
*/
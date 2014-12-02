<?php

Admin::model('Country')->title('国家列表')->columns(function ()
{
    Column::string('id', '编号');
    Column::string('c_name', '中文名');
    Column::date('e_name', '英文名');
})
->form(function (){
    FormItem::text('c_name', '中文名')->required(true);
    FormItem::text('e_name', '英文名')->required(true);
});
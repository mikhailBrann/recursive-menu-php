<?php
    const DB_NAME = 'products';
    const DB_USER = 'test';
    const DB_PASS = 'test';

    const DB_CONTENT = [
        ['name' =>'оВощи'],
        ['name' =>'Фрукты'],
        ['name' =>'Мясо'],
        ['name' =>'Алкоголь'],
        ['name' =>'помидор', 'parent' => 'ОвоЩи'],
        ['name' =>'курица', 'parent' =>  'мясо'],
        ['name' =>'говядина', 'parent' =>  'мясо'],
        ['name' =>'котлеты', 'parent' =>  'курица'],
        ['name' =>'филе', 'parent' =>  'курица'],
        ['name' =>'лопатка', 'parent' =>  'говядина'],
        ['name' =>'котлеты', 'parent' =>  'говядина']
    ];
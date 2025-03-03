<?php

return [
    'userManagement' => [
        'title'          => 'Manajemen User',
        'title_singular' => 'Manajemen User',
    ],
    'permission' => [
        'title'          => 'Izin',
        'title_singular' => 'Izin',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Title',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'role' => [
        'title'          => 'Peranan',
        'title_singular' => 'Peranan',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Title',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
            'permissions_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'user' => [
        'title'          => 'Daftar Pengguna',
        'title_singular' => 'User',
        'fields'         => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'bu'                       => 'BU',
            'bu_helper'                => ' ',
            'dept'                     => 'BU - dept/site',
            'dept_helper'              => ' ',
            'username'                 => 'Username',
            'username_halper'          => '',
            'name'                     => 'Name',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'phone'                    => 'Phone',
            'phone_helper'             => ' ',
            'email_verified_at'        => 'Email verified at',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Password',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Remember Token',
            'remember_token_helper'    => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
        ],
    ],
    'bu' => [
        'title'          => 'BU',
        'title_singular' => 'BU',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'code'              => 'Code',
            'code_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'buDept' => [
        'title'          => 'Bu & Dept',
        'title_singular' => 'Bu & Dept',
    ],
    'dept' => [
        'title'          => 'Dept',
        'title_singular' => 'Dept',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'code'              => 'Code',
            'code_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'bu'                => 'BU',
            'bu_helper'         => ' ',
        ],
    ],
    'pkab' => [
        'title'          => 'PKAB',
        'title_singular' => 'PKAB',
    ],
    'pkabItem' => [
        'title'          => 'PKAB Dalam Proses',
        'title_singular' => 'PKAB',
        'selesai'        => 'Selesai',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'code'              => 'Kode',
            'code_helper'       => ' ',
            'req_date'          => 'Tgl dibutuhkan',
            'req_date_helper'   => ' ',
            'user'              => 'User',
            'user_helper'       => ' ',
            'status'            => 'Status',
            'status_helper'     => ' ',
            'created_at'        => 'Tgl dibuat',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'bu'                => 'BU',
            'bu_helper'         => ' ',
            'dept'              => 'Dept',
            'dept_helper'       => ' ',
            'site'              => 'Site',
            'site_helper'       => ' ',
        ],
    ],
    'item' => [
        'title'          => 'Item',
        'title_singular' => 'Item',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',

            'pkab'               => 'Pkab',
            'pkab_helper'        => ' ',
            'name'               => 'Name',
            'name_helper'        => ' ',
            'merk'               => 'Merk',
            'merk_helper'        => ' ',
            'spesifikasi'        => 'Spesifikasi',
            'spesifikasi_helper' => ' ',
            'qty'                => 'Qty',
            'qty_helper'         => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'statusHistory' => [
        'title'          => 'Status History',
        'title_singular' => 'Status History',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'pkab'              => 'Pkab',
            'pkab_helper'       => ' ',
            'status'            => 'Status',
            'status_helper'     => ' ',
            'user'              => 'User',
            'user_helper'       => ' ',
            'date'              => 'Date',
            'date_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'site' => [
        'title'          => 'Site',
        'title_singular' => 'Site',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'code'              => 'Code',
            'code_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'bu'                => 'BU',
            'bu_helper'         => ' ',
        ],
    ],
    'marketlist' => [
        'title'             => 'MARKET LIST',
        'title_singular'    => 'MARKET LIST',
        'selesai'           => 'Selesai',
        'fields'            => [
            'id'                    => 'ID',
            'id_helper'             => ' ',
            'code'                  => 'ID',
            'code_helper'           => ' ',
            'status'                => 'Status',
            'status_helper'         => ' ',
            'user'                  => 'User',
            'user_helper'           => ' ',
            'bu'                    => 'BU',
            'bu_helper'             => ' ',
            'dept'                  => 'Dept',
            'dept_helper'           => ' ',
            'site'                  => 'Site',
            'site_helper'           => ' ',
            'event'                 => 'Event',
            'event_helper'          => ' ',
            'required_date'         => 'Tanggal Dibutuhkan',
            'required_date_helper'  => ' ',
            'qty'                   => 'Qty',
            'qty_helper'            => '',
            'approved_qty'          => 'Approved Qty',
            'approved_qty_helper'   => '',
            'approved_date'         => 'Approved Date',
            'approved_date_helper'  => '',            
            'created_at'            => 'Created at',
            'created_at_helper'     => ' ',
            'updated_at'            => 'Updated at',
            'updated_at_helper'     => ' ',
            'deleted_at'            => 'Deleted at',
            'deleted_at_helper'     => ' ',
        ]
    ],
    'marketlistItem' => [
        'title'             => 'Marketlist Item',
        'title_singular'    => 'Marketlist Item',
        'selesai'           => 'Selesai',
        'fields'            => [
            'id'                    => 'ID',
            'id_helper'             => ' ',
            'name'                  => 'Name',
            'name_helper'           => ' ',
            'category'              => 'Category',
            'category_helper'       => ' ',
            'created_at'            => 'Created at',
            'created_at_helper'     => ' ',
            'updated_at'            => 'Updated at',
            'updated_at_helper'     => ' ',
            'deleted_at'            => 'Deleted at',
            'deleted_at_helper'     => ' ',
            'site'                  => 'Site',
            'site_helper'           => ' ',
        ]
    ],

];
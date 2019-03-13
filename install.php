<?php

 rex_sql_table::get(rex::getTable('user'))
     ->ensureColumn(new rex_sql_column('minibar', 'tinyint', false), 'admin')
     ->alter();

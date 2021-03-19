<?php
    require_once __DIR__.'/utils/Options.php';
    require_once __DIR__.'/utils/DataTypes.php';
    require_once __DIR__.'/utils/Field.php';

    require_once __DIR__.'/ORM.php';
    $orm = new ORM('YOUR HOST', 'YOUR USER', 'YOUR PASSWORD', 'YOUR DATABSE');

    $users = $orm->define(array(
        'name' => 'users',
        'fields' => array(
            'id' => new Field(
                DataTypes::INT,
                false,
                true
            ),
            'name' => new Field(
                DataTypes::STRING,
                false
            ),
        ),
        'options' => new Options('id')
    ));

    $tells = $orm->define(array(
        'name' => 'tells',
        'fields' => array(
            'id' => new Field(
                DataTypes::INT,
                false,
                true
            ),
            'user' => new Field(
                DataTypes::INT,
                false
            ),
            'tell' => new Field(
                DataTypes::STRING,
                false
            )
        ),
        'options' => new Options(
            'id',
            false,
            true,
            array(
                array(
                    'field' => 'user',
                    'references' => array(
                        'table' => 'users',
                        'field' => 'id'
                    ),
                    'change' => 'CASCADE'
                )
            )
        )
    ));
?>

<?php
    // header("content-type: application/json");

    require_once __DIR__.'/utils/Options.php';
    require_once __DIR__.'/utils/DataTypes.php';
    require_once __DIR__.'/utils/Field.php';

    require_once __DIR__.'/ORM.php';
    $orm = new ORM('YOUR HOST', 'YOUR USER', 'YOUR PASSWORD', 'YOUR DATABSE');

    $user = $orm->define(array(
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
            )
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
            'owner' => new Field(
                DataTypes::INT,
                false
            ),
            'tell' => new Field(
                DataTypes::INT,
                false
            )
        ),
        'options' => new Options(
            'id',
            false,
            true,
            array(
                'field' => 'owner',
                'references' => array(
                    'table' => 'users',
                    'field' => 'id'
                ),
                'change' => 'CASCADE'
            )
        )
    ));

    // echo json_encode($tells->table);

?>

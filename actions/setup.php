<?php
include( '../includes/default.php' );

// http://steamcommunity.com/app/346110/discussions/0/594821545178546859/
$developers = array(
    array(
        'nick' => 'Jat',
        'name' => '',
        'role' => 'Community Manager',
        'accounts' => array(
            'steam' => 'wildcardjat',
            'reddit' => 'jatonreddit',
            'twitter' => 'jatstweeter'
        )
    ),
    array(
        'nick' => 'The Right Hand',
        'name' => 'Kayd Hendricks',
        'role' => '',
        'accounts' => array(
            'steam' => '76561198175441809',
            'reddit' => 'WildcardTheRightHand',
            'twitter' => 'KaydHendricks'
        )
    ),
    array(
        'nick' => 'The Left Hand',
        'name' => '',
        'role' => 'Designer',
        'accounts' => array(
            'steam' => '76561198178808059'
        )
    ),
    array(
        'nick' => 'Drake',
        'name' => '',
        'role' => 'Lead Engineer',
        'accounts' => array(
            'steam' => 'wildcarddrake'
        )
    ),
    array(
        'nick' => 'Studio Wildcard',
        'name' => '',
        'role' => '',
        'accounts' => array(
            'steam' => '76561198207415608'
        )
    ),
    array(
        'nick' => 'Ark Dev Engineer',
        'name' => '',
        'role' => 'Engineer',
        'accounts' => array(
            'steam' => '76561198229320228'
        )
    ),
    array(
        'nick' => 'The Right Foot',
        'name' => '',
        'role' => 'Producer',
        'accounts' => array(
            'steam' => '76561198178797193'
        )
    ),
    array(
        'nick' => 'Jesse Rapczak',
        'name' => 'Jesse Rapczak',
        'role' => 'Art Director',
        'accounts' => array(
            'steam' => '76561198174992766'
        )
    ),
    array(
        'nick' => 'RawMeat',
        'name' => '',
        'role' => '',
        'accounts' => array(
            'steam' => '76561198212514456'
        )
    ),
    array(
        'nick' => 'OMoussa',
        'name' => '',
        'role' => '',
        'accounts' => array(
            'steam' => 'OMoussaInstinct'
        )
    ),
    array(
        'nick' => 'The Left Foot',
        'name' => '',
        'role' => '',
        'accounts' => array(
            'steam' => 'wcTLF'
        )
    )
);

// Create posts table if it doesn't exist
$tableExists = gettype( $database->exec( 'SELECT count(*) FROM posts LIMIT 1' ) ) == 'integer';
if( !$tableExists ):
    $query = 'CREATE TABLE posts( topic TEXT, topic_url TEXT, uid TEXT, url TEXT, source TEXT, content TEXT, timestamp TEXT )';
    $database->exec( $query );
endif;

// Create developers table if it doesn't exist
$tableExists = gettype( $database->exec( 'SELECT count(*) FROM developers LIMIT 1' ) ) == 'integer';
if( !$tableExists ):
    $query = 'CREATE TABLE developers( id INT PRIMARY KEY, nick TEXT, name TEXT, role TEXT )';
    $database->exec( $query );
endif;

// Create accounts table if it doesn't exist
$tableExists = gettype( $database->exec( 'SELECT count(*) FROM accounts LIMIT 1' ) ) == 'integer';
if( !$tableExists ):
    $query = 'CREATE TABLE accounts( uid INT, service TEXT, identifier TEXT )';
    $database->exec( $query );
endif;


// Database stuff for developer
$userExistsQuery = 'SELECT id FROM developers WHERE nick = :nick LIMIT 1';
$userExistsPDO = $database->prepare( $userExistsQuery );

$createUserQuery = 'INSERT INTO developers ( id, nick, name, role ) VALUES( :id, :nick, :name, :role )';
$createUserPDO = $database->prepare( $createUserQuery );

$updateUserQuery = 'UPDATE developers SET nick = :nick, name = :name, role = :role WHERE id = :id';
$updateUserPDO = $database->prepare( $updateUserQuery );

// Database stuff for accounts
$accountExistsQuery = 'SELECT COUNT(*) FROM accounts WHERE service = :service AND uid = :uid LIMIT 1';
$accountExistsPDO = $database->prepare( $accountExistsQuery );

$createAccountQuery = 'INSERT INTO accounts ( uid, service, identifier ) VALUES( :uid, :service, :identifier)';
$createAccountPDO = $database->prepare( $createAccountQuery );

$updateAccountQuery = 'UPDATE accounts SET identifier = :identifier WHERE uid = :uid AND service = :service';
$updateAccountPDO = $database->prepare( $updateAccountQuery );

// Current max value uid
$highetstUidPDO = $database->query( 'SELECT id FROM developers ORDER BY id DESC LIMIT 1' );
$highestUid = $highetstUidPDO->fetchColumn();

if( !$highestUid ) :
    $highestUid = 0;
endif;

foreach( $developers as $developer ) :
    $userExistsPDO->bindValue( ':nick', $developer[ 'nick' ] );
    $userExistsPDO->execute();

    $uid = $userExistsPDO->fetchColumn();
    if( $uid > 0 ) :
        // User already exists
        $updateUserPDO->bindValue( ':id', $uid );
        $updateUserPDO->bindValue( ':nick', $developer[ 'nick' ] );
        $updateUserPDO->bindValue( ':name', $developer[ 'name' ] );
        $updateUserPDO->bindValue( ':role', $developer[ 'role' ] );

        $updateUserPDO->execute();
    else :
        $uid = $highestUid + 1;
        // User doesn't exist
        $createUserPDO->bindValue( ':id', $uid );
        $createUserPDO->bindValue( ':nick', $developer[ 'nick' ] );
        $createUserPDO->bindValue( ':name', $developer[ 'name' ] );
        $createUserPDO->bindValue( ':role', $developer[ 'role' ] );

        $createUserPDO->execute();
    endif;

    $createAccountPDO->bindValue( ':uid', $uid );
    $updateAccountPDO->bindValue( ':uid', $uid );

    foreach( $developer[ 'accounts' ] as $service => $identifier ) :
        $accountExistsPDO->bindValue( ':service', $service );
        $accountExistsPDO->bindValue( ':uid', $uid );
        $accountExistsPDO->execute();

        if( $accountExistsPDO->fetchColumn() > 0 ) :
            // Account already exists
            $updateAccountPDO->bindValue( ':service', $service );
            $updateAccountPDO->bindValue( ':identifier', $identifier );
            $updateAccountPDO->execute();
        else :
            // Account doesn't exist
            $createAccountPDO->bindValue( ':service', $service );
            $createAccountPDO->bindValue( ':identifier', $identifier );
            $createAccountPDO->execute();
        endif;

    endforeach;

    $highestUid = $highestUid + 1;
endforeach;
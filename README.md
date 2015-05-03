# ppmoney
Interface de don/adhésion/financement participatif du Parti Pirate

# conf/config.php

    <?php
    if(!isset($config)) {
    	$config = array();
    }

    $config["database"] = array();
    $config["database"]["host"] = "";
    $config["database"]["port"] = ;
    $config["database"]["login"] = "";
    $config["database"]["password"] = "";
    $config["database"]["database"] = "";
    $config["database"]["prefix"] = "";

    $config["server"] = array();
    $config["server"]["base"] = "http://don.partipirate.org/";
    // The server line, ex : dev, beta - Leave it empty for production
    $config["server"]["line"] = "dev";

    $config["galette"] = array();
    $config["galette"]["database"] = "galette_test";

    ?>

# conf/mail.config.php

    <?php
    if(!isset($config)) {
        $config = array();
    }

    $config["smtp"] = array();

    $config["smtp"]["host"] = "";
    $config["smtp"]["port"] = "";
    $config["smtp"]["username"] = "";
    $config["smtp"]["password"] = "";
    $config["smtp"]["from.address"] = "adhesion-don@partipirate.org";
    $config["smtp"]["from.name"] = "Parti Pirate";

    ?>

# conf/paybox.php

    <?php
    if(!isset($config)) {
    	$config = array();
    }

    // clef secrète du commerçant
    $config["paybox"]["secretKey"] = "mettre ici votre clé secrète";
    // URL du serveur de télépaiement
    $config["paybox"]["server"] = "tpeweb.paybox.com";

    $config["paybox"]["PBX_SITE"] = "mettre ici l'id site";
    $config["paybox"]["PBX_RANG"] = "mettre ici le rang";
    $config["paybox"]["PBX_IDENTIFIANT"] = "mettre ici l'identifiant";

    $config["paybox"]["allowed_ips"] = array("195.101.99.73", "195.101.99.76",
                                            "194.2.160.66", "194.2.122.158",
                                            "195.25.7.146", "195.25.7.166");

    $config["paybox"]["pem"] = "-----BEGIN PUBLIC KEY-----
    MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDe+hkicNP7ROHUssGNtHwiT2Ew
    HFrSk/qwrcq8v5metRtTTFPE/nmzSkRnTs3GMpi57rBdxBBJW5W9cpNyGUh0jNXc
    VrOSClpD5Ri2hER/GcNrxVRP7RlWOqB1C03q4QYmwjHZ+zlM4OUhCCAtSWflB4wC
    Ka1g88CjFwRw/PB9kwIDAQAB
    -----END PUBLIC KEY-----";

    ?>

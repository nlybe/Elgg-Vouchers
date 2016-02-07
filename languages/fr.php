<?php
/**
 * Elgg vouchers plugin
 *
 * @package Vouchers
 */

$french = array(

    // menu items and titles
    'vouchers' => 'Coupons',
    'vouchers:menu' => 'Coupons',
    
    // basic options
    'vouchers:add' => "Créer un coupon",
    'vouchers:edit' => "Modifier un coupon",
    'vouchers:unknown_voucher' => "Coupon inconnu",
    'vouchers:print' => "Imprimer le coupon",
    'vouchers:printthis' => "Imprimet cette page",
    'item:object:vouchers' => "Coupons",
    'item:object:' => "Coupons",
    'vouchers:by' => "Coupon de",
    'vouchers:voucher' => "Coupon",
    'vouchers:list:list' => "Afficher par liste",
    'vouchers:list:gallery' => "Afficher en tableau",  
    'vouchers:howmany' => "Aucun coupon en cours",  
    'vouchers:send_message' => "Contacter le vendeur",

    // Status messages
    'vouchers:none' => "Aucun coupon",
    'vouchers:owner' => "Coupons de %s",
    'vouchers:friends' => "Amis",
    'vouchers:save:success' => "Coupon créé avec succès.",
    'vouchers:save:missing_title' => "Donner un titre à votre coupon.",
    'vouchers:save:missing_code' => "Le code du coupon est manquant.",
    'vouchers:save:missing_amount' => "Le montant doit être précisé.",
    'vouchers:save:amount_not_numeric' => "Le montant doit être une valeur numérique.",
    'vouchers:save:until_date_lessthan_valid_from' => "La fin de l'offre est antérieure à la date de lancement.",    
    'vouchers:save:missing_until_date' => "La fin de validité du coupon doit être indiquée.",
    'vouchers:save:failed' => "La tentative de création d'une nouveau coupon a échoué.",
    'vouchers:delete:success' => "Votre coupon a été supprimé.",    
    'vouchers:delete:failed' => "Impossible de supprimer votre coupon.",   
    'vouchers:expired' => "Le coupon n'est plus valable.",
    'vouchers:expired_noprint' => "Le coupon n'est plus valable. Il est impossible de l'imprimer.",
    'vouchers:save:novalid_weburl' => "L'adresse du site n'est pas valable (http:\\www.votresite.net)",
    'vouchers:save:howmany_not_numeric' => "La quantité doit être une valeur numérique.",
    
    // add vouchers function
    'vouchers:addvoucher' => "Créer un coupon",
    'vouchers:addvoucher:title' => "Titre",
    'vouchers:addvoucher:title:note' => "Titre du coupon.",
    'vouchers:addvoucher:description' => "Description",
    'vouchers:addvoucher:description:note' => "Description du coupon.",
    'vouchers:addvoucher:validfrom' => "Disponible du ",
    'vouchers:addvoucher:validuntil' => "Disponible jusqu'à",
    'vouchers:addvoucher:location' => "Lieu",
    'vouchers:addvoucher:tags' => "Mots-clés",
    'vouchers:addvoucher:submit' => "Enregistrer",
    'vouchers:addvoucher:missingtitle' => "Renseigner le titre",
    'vouchers:addvoucher:cannotload' => "Impossible de charger le coupon",
    'vouchers:addvoucher:noaccess' => "Accès réservé.",
    'vouchers:addvoucher:noaccessforpost' => "Vous n'avez pas les droits qui permettent de créer un coupon.",
    'vouchers:addvoucher:code' => "Code du coupon",
    'vouchers:addvoucher:code:login' => "Merci de vous identifier pour obtenir le code du coupon.",
    'vouchers:addvoucher:code:note' => "Indiquer le code dont l'utilisateur aura besoin pour bénéficier de ce coupon dans votre boutique. ",
    'vouchers:addvoucher:amount' => "Rabais",
    'vouchers:addvoucher:amount:note' => "Montant de rabais lié à ce coupon.",
    'vouchers:addvoucher:amount_type' => "Couponcorrespondant à",
    'vouchers:addvoucher:amount_type:note' => "Indiquer si c'est un % de réduction ou un montant donné.",
    'vouchers:addvoucher:validfrom' => "Disponible dès",
    'vouchers:addvoucher:total' => "Total",
    'vouchers:addvoucher:percentage' => "Pourcentage",
    'vouchers:addvoucher:howmany' => "Coupon limité à ",
    'vouchers:addvoucher:howmany:note' => "Coupon encore disponible. Laisser cette case vierge pour indiquer que l'offre de coupon n'est pas limitée en nombre de bénéficiaires.",
    'vouchers:addvoucher:howmany_show' => "Coupon restant",
    'vouchers:addvoucher:requiredfields' => "les cases avec * sont requises",
    'vouchers:addvoucher:currency' => "Unité monétaire",
    'vouchers:addvoucher:weburl' => "Adresse web de votre boutique",
    'vouchers:addvoucher:weburl:note' => "Indiquer le site web où profiter des avantages de ce coupon.",

    // river
    'river:create:object:vouchers' => '%s a créé le coupon %s',
    'river:comment:object:vouchers' => '%s a un avis concernant le coupon %s',
    'vouchers:river:annotate' => 'un avis concernant ce coupon',
    'vouchers:river:item' => 'un sujet',  
    
    // groups
    'vouchers:group' => 'Coupons pour Groupes',
    'vouchers:group:enablevouchers' => 'Coupons actifs pour les groupes',
    
    // settings
    'vouchers:settings:defaultdateformat' => 'Format de date par défaut',
    'vouchers:settings:defaultdateformat:note' => 'Indiquer le format d\'affichage des dates.', 
    'vouchers:settings:defaultcurrency' => 'Unité monétaire par défaut',
    'vouchers:settings:defaultcurrency:note' => 'Indiquer l\'unité monétaire de ce coupon',
    'vouchers:settings:uploaders' => 'Qui peut créer des coupons ?',
    'vouchers:settings:uploaders:note' => 'Définir les droits liés à la création de coupons.',
    'vouchers:settings:uploaders:allmembers' => 'Tous les membres',
    'vouchers:settings:uploaders:admins' => 'Webmestres',
    'vouchers:settings:send_message' => 'Autoriser les contacts entre acheteurs et vendeurs',
    'vouchers:settings:send_message:note' => 'Indiquer si un membre peut contacter un acheteur via la messagerie interne.',    
    'vouchers:settings:no' => "Non",
    'vouchers:settings:yes' => "Oui",       

    // widget
    'vouchers:widget' => 'Mes coupons',
    'vouchers:widget:viewall' => "Afficher tous mes coupons",
    'vouchers:widget:num_display' => "Nombre de coupons à afficher",
    'vouchers:widget:price' => "Prix",
    'vouchers:widget:amount' => "Rabais",
    'vouchers:widget:validuntil' => "Date de validité",
        
    // only in pro version
    'vouchers:settings:paypal_account' => 'Paypal : Merchant ID ou adresse électronique',
    'vouchers:settings:paypal_account:note' => 'Indiquer l\'identifiant "Merchant ID" ou l\'adresse électronique liée à votre compte Paypal. Ce compte servira à collecter les règlements via Paypal<strong> dans le cas où SEUL l\'administrateur est habilité à créer des annonces.</strong>. Dans le cas contraire, tous les membres dont l\'adresse électronique utilisée pour ce site est celle qui est liée au compte Paypal.',
    'vouchers:addvoucher:price' => "Montant du coupon",
    'vouchers:addvoucher:price:note' => "Prix à payer via Paypal pour bénéficier du coupon. Laisser la case vierge si le coupon est gratuit.",
    'vouchers:buy' => "Acheter le coupon",
    'vouchers:save:price_not_numeric' => "Le montant correspond à une valeur numérique.",
    'vouchers:sales' => "Ventes de ce coupon",
    'vouchers:transactionid' => "Numéro de transaction",
    'vouchers:messagetobuyer' => "(vous avez déjà acheté ce coupon)",
    'vouchers:paypal:buyeremail' => "Adresse e-mail de l'acheteur",
    'vouchers:paypal:country' => "Pays",
    'vouchers:paypal:firstname' => "Prénom",
    'vouchers:paypal:lastname' => "Nom",
    'vouchers:paypal:mccurrency' => "Devise",
    'vouchers:paypal:mcgross' => "Montant",
    'vouchers:paypal:paymentdate' => "Date de paiement",
    'vouchers:paypal:paymentstatus' => "Etat du paiement",
    'vouchers:paypal:sellersubject' => "Ordre vérifié par ",
    'vouchers:paypal:buyersubject' => "Transaction réussie",
    'vouchers:paypal:buyerbody' => "Pour lire ou imprimer le justificatif du coupon, cliquer sur le lien ci-dessous :",
    'vouchers:paypal:title' => "Coupon",
    'vouchers:buyerprofil' => "Portrait de l'acheteur",
    'vouchers:ipn:title' => "Echec de la vérification IPN contre la fraude",
    'vouchers:ipn:error1' => "Opération de paiement incomplète",
    'vouchers:ipn:error2' => "mc_gross ne correspond pas : ",
    'vouchers:ipn:error3' => "mc_currency ne correspond pas: ",
    'vouchers:ipn:error4' => "Cet utilisateur a déjà acheté ce coupon :",
    'vouchers:ipn:error5' => "Achat impossible à enregistrer. Contacter le webmestre.",
    'vouchers:ipn:error6' => "cet acheteur n'est pas membre de ce site",
    'vouchers:ipn:error7' => "item_number non paramétré",
    'vouchers:settings:sandbox:no' => "Non",
    'vouchers:settings:sandbox:yes' => "Oui",
    'vouchers:settings:sandbox:note' => "Choisir <strong>Oui</strong> UNIQUEMENT à des fins expérimentales en utilisant un compte \"sandbox\" (bac à sable). ",
    'vouchers:settings:sandbox' => "Utiliser le Bac à sable (Sandbox - Mode test)",
    'vouchers:addvoucher:price:note:importantall' => ' 
	<!-- PayPal Logo -->
	<table border="0" cellpadding="10" cellspacing="0" align="left">
	<tr>
	<td align="left">
	Les paiements via Paypal sont effectués en utilisant l\'adresse e-mail utilisée lors de l\'inscription à ce site
	<br>Il convient alors que cette adresse e-mail soit liée à un compte Paypal.
        </td>
	</tr>
	<tr>
	<td align="left">
	<a href="#" title="PayPal Comment Ca Marche" onclick="javascript:window.open(\'https://www.paypal.com/fr/webapps/mpp/paypal-popup\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600\');">
	<img src="https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_paiements_fr.png" border="0" alt="Paiements avec PayPal">
	</a>
	<div style="text-align:left">
	<a href="https://www.paypal.com/fr/webapps/mpp/accueil-professionnel"  target="_blank"><font size="2" face="Arial" color="#0079CD"><b>PayPal Comment Ca Marche ?</b></font></a>
	</div>
	</td>
	</tr>
	
	</table><!-- PayPal Logo -->',
	
	
	
	
    'vouchers:addvoucher:price:note:importantadmin' => "(Un compte PayPal est indispensable pour les webmestres)",
    
);

add_translation("fr", $french);
?>

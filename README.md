Description
----
This plugin provides waardepieren functionality for wordpress, it has an optional support in place for grafity forms but can be used without it

## About Waardepapieren

The waardenpapieren project aims at digitizing proof from the dutch government for its citizens (e.g. birth certificates, marriage certificates and proof of residence and residential history) it is based on the [W3C claims structure](https://w3c.github.io/vc-data-model/#claims) for standardization.

At the core of the waardepapieren concept is that a “proof” should be applicable both digital and non-digital. Therefore a proof is presented as a PDF containing an JTW based claim, the claim itself however can also be used separately. For more information about the inner workings of waardepapieren see the waardepapieren service at it [repro]( https://github.com/ConductionNL/waardepapieren-service).

## Online test environment
There are several online environments available for testing

1. [Example user interface](https://waardepapieren-gemeentehoorn.commonground.nu)
2. [Example registration desk interface](https://waardepapieren-gemeentehoorn.commonground.nu/waardepapieren-balie)
3. [Example Wordpress implementation](https://dev.zuiddrecht.nl)
4. [Example Waardepapieren Service](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar)
5. [Example Waardepapieren Registration](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/wari )

## Dependencies

For this repository you will need an API key at a waardepapieren service, a valid api key can be obtained at [Dimpac](https://www.dimpact.nl/) a key for the test api can be obtained from [Conduction](https://condution.nl).

## Installation 
This Wordpress plugin can be installed from the [wordpress plugin store](https://wordpress.org/plugins/).

Afther installation and activation the plugin must be configures before it can be used. 

## Other Repro’s
*UI*
1. [Burger interface](https://github.com/ConductionNL/waardepapieren) 
2. [Ballie interface](https://github.com/ConductionNL/waardepapieren-ballie)

*Componenten*
1. [Motorblok](https://github.com/ConductionNL/waardepapieren-service) 
2. [Register](https://github.com/ConductionNL/waardepapieren-register) 

*Libraries*
1. [PHP](https://github.com/ConductionNL/waardepapieren-php)

*Plugins*
1. [Wordpress](https://github.com/ConductionNL/waardepapieren_wordpress) 
2. [Typo3](https://github.com/ConductionNL/waardepapieren_typo3) 
3. [Drupal](https://github.com/ConductionNL/waardepapieren_drupal) 


Credits
----

Information about the authors of this component can be found [here](AUTHORS.md)

This component is based on the [ICTU discipl project](https://github.com/discipl/waardepapieren)

Copyright © [Dimpact](https://www.dimpact.nl/) 2020

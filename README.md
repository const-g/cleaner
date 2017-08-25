# Cleaner

A lib to clean domains and company names. It also can extract the acronym of the company (or try to)

## Install

```bash
composer require const-g/cleaner
```

## Usage:

It removes common 'company' terms in different languages _(not all yet)_

```php
<?php
$company_name = \Constg\Cleaner\Clean::company_names('Google inc.');
// array(
//     'original_name' => $company_name,
//     'split' => array('google'),
//     'clean_name' => 'google',
//     'clean_name_no_space' => 'google',
// );
```

It also removes country name

```php
<?php
$company_name = \Constg\Cleaner\Clean::company_names('Google France');
// array(
//     'original_name' => $company_name,
//     'split' => array('google'),
//     'clean_name' => 'google',
//     'clean_name_no_space' => 'google',
// );
```

    Known bug: does not cover company name like "Electricité de France" as 
    it will be converted to "electricite" only.

Can recompose spaced acronym

```php
<?php
$company_name = \Constg\Cleaner\Clean::company_names('B.M.W Group');
// array(
//     'original_name' => 'B.M.W Group',
//     'split' => array('bmw'),
//     'clean_name' => 'bmw',
//     'clean_name_no_space' => 'bmw',
// );
```

If the acronym is present, then it extract it from the name, if it matches the rest of the name

```php
<?php
$company_name = \Constg\Cleaner\Clean::company_names('COMPAGNIE GENERALE DES ETABLISSEMENTS MICHELIN (C G E M)');
// array(
//     'original_name' => 'COMPAGNIE GENERALE DES ETABLISSEMENTS MICHELIN (C G E M)',
//     'split' => array('compagnie', 'etablissements', 'generale', 'michelin'),
//     'clean_name' => 'compagnie etablissements generale michelin',
//     'clean_name_no_space' => 'compagnieetablissementsgeneralemichelin',
//     'acronym' => 'cgem',
// );
```


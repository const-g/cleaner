<?php
namespace Constg\Cleaner\Tests;

use Constg\Cleaner\Clean;

require __DIR__ . '/../vendor/autoload.php';

class CleanCompanyNamesTest extends \PHPUnit_Framework_TestCase
{
    public function test_valid() {
        $company_name = 'Google';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('google'),
            'clean_name' => 'google',
            'clean_name_no_space' => 'google',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_trimmed() {
        $company_name = '  Google  ';
        $expected = array(
            'original_name' => 'Google',
            'split' => array('google'),
            'clean_name' => 'google',
            'clean_name_no_space' => 'google',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_camelCase() {
        $company_name = 'MySuperCompany';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('company', 'my', 'super'),
            'clean_name' => 'company my super',
            'clean_name_no_space' => 'companymysuper',
            'possible_acronym' => 'msc',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_multi_space() {
        $company_name = 'My  Super    Company';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('company', 'my', 'super'),
            'clean_name' => 'company my super',
            'clean_name_no_space' => 'companymysuper',
            'possible_acronym' => 'msc',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_stop_words() {
        $company_name = 'Google France';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('google'),
            'clean_name' => 'google',
            'clean_name_no_space' => 'google',
        );
        $this->assertEquals(
            $expected,
            Clean::company_names($company_name)
        );
    }

    public function test_custom_stop_words() {
        $company_name = 'Google custom';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('google'),
            'clean_name' => 'google',
            'clean_name_no_space' => 'google',
        );
        $this->assertEquals(
            $expected,
            Clean::company_names($company_name, array('Custom'))
        );
    }

    public function test_acronym_space() {
        $company_name = 'B.M.W Group';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('bmw'),
            'clean_name' => 'bmw',
            'clean_name_no_space' => 'bmw',
        );
        $this->assertEquals(
            $expected,
            Clean::company_names($company_name)
        );
    }

    public function test_remove_acronym() {
        $company_name = 'My Super Company (msc)';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('company', 'my', 'super'),
            'clean_name' => 'company my super',
            'clean_name_no_space' => 'companymysuper',
            'acronym' => 'msc',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));

        // 2
        $company_name = 'MySuperCompany (msc)';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('company', 'my', 'super'),
            'clean_name' => 'company my super',
            'clean_name_no_space' => 'companymysuper',
            'acronym' => 'msc',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));

        // 3
        $company_name = 'MSC - MySuperCompany';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('company', 'my', 'super'),
            'clean_name' => 'company my super',
            'clean_name_no_space' => 'companymysuper',
            'acronym' => 'msc',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));

        // 4
        $company_name = 'COMPAGNIE GENERALE DES ETABLISSEMENTS MICHELIN (C G E M )';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('compagnie', 'etablissements', 'generale', 'michelin'),
            'clean_name' => 'compagnie etablissements generale michelin',
            'clean_name_no_space' => 'compagnieetablissementsgeneralemichelin',
            'acronym' => 'cgem',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_special_char() {
        $company_name = 'L\'OREAL SA';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('loreal'),
            'clean_name' => 'loreal',
            'clean_name_no_space' => 'loreal',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_special_char_2() {
        $company_name = 'SÜDWESTDEUTSCHE MEDIEN HOLDING GMBH';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('medien', 'sudwestdeutsche'),
            'clean_name' => 'medien sudwestdeutsche',
            'clean_name_no_space' => 'mediensudwestdeutsche',
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }

    public function test_special_char_3() {
        $company_name = 'A P MØLLER - MÆRSK AS';
        $expected = array(
            'original_name' => $company_name,
            'split' => array('ap', 'as', 'maersk', 'moller'),
            'clean_name' => 'ap as maersk moller',
            'clean_name_no_space' => 'apasmaerskmoller',
            'possible_acronym' => 'amma'
        );
        $this->assertEquals($expected, Clean::company_names($company_name));
    }
}

<?php

namespace Constg\Cleaner;

class Clean
{
    /**
     * @param $domain_name
     * @param int $allowed_subdomains
     *
     * @return string
     */
    public static function domain($domain_name, $allowed_subdomains = 0)
    {
        // https://en.wikipedia.org/wiki/Second-level_domain
        // https://publicsuffix.org/list/effective_tld_names.dat
        $allowed_tld       = array(
            '[a-zA-Z0-9]{2,8}$',
            '(net|mil)\.ac$',                                                       // ac
            '(ac|ltd|mod|mil|net|nhs|parliament|plc|police|sch)\.uk$',              // UK
            '(ac|nhs|plc|police|sch)\.za$',                                         // ZA
            '(asn|net|id|csiro|act|nsw|nt|qld|sa|tas|vic|wa)\.au$',                 // Australia
            '(or|priv|ac|gv)\.at$',                                                 // Austria
            '(adm)\.br$',                                                           // Brazil
            '(asso|avocat|aeroport|cci|com|huissier-justice|nom|presse|tm)\.fr$',   // France
            '(govt|health|mil)\.nz$',                                               // New Zeland
            '(co|com|edu|gov|gouv|me|org)\.[\w]{2,8}$',                             // Generic
        );
        $allowed_tld_regex = '[a-zA-Z0-9-]+\.(' . implode('|', $allowed_tld) . ')';
        if ($allowed_subdomains > 0) {
            for ($i = 0; $i < $allowed_subdomains; $i++) {
                $allowed_tld_regex = "([a-zA-Z0-9-]+\.)$allowed_tld_regex";
            }
        }

        $domain_name = trim($domain_name);

        preg_match("/$allowed_tld_regex/", $domain_name, $__domain_name);

        return ! empty($__domain_name[0]) ? $__domain_name[0] : null;
    }

    /**
     * Help from:
     * - https://www.quora.com/What-are-good-ways-to-clean-up-a-large-collection-of-user-entered-company-names
     * - https://github.com/OpenRefine/OpenRefine/wiki/Clustering-In-Depth
     * - http://www.regular-expressions.info/unicode.html
     * - http://stackoverflow.com/questions/6572974/regex-to-break-up-camelcase-string-php/6572999#6572999
     * - https://en.wikipedia.org/wiki/Types_of_business_entity
     * - https://en.wikipedia.org/wiki/Private_limited_company
     *
     * @param string $company_name
     * @param array $custom_stop_words  a list of words to add to the default list
     *
     * @return array
     */
    public static function company_names($company_name = '', array $custom_stop_words = array())
    {
        // stop-words - countries names
        $stop_words = array(
            'emea',
            'usa',
            'italy', 'italia',
            'spain', 'espana',
            'france',
            'germany', 'deutschland',
        );

        // stop-words - English
        $stop_words = array_merge(array(
            'limited',
            'financial',
            'services',
            'group',
            'holding',
            'ltd',
            'pllc',
            'llc',
            'cc',
            'llp',
            'lp',
            'cyf',
            'ccc',
            'plc',
            'pty',
            'inc',
            'cic',
            'cio',
            'Unltd',
            'Incorporated',
            'corporation',
            'corp',
            'office',
            'realty',
            'the',
            'a',
            'uk',
        ), $stop_words);

        // stop-words - French
        $stop_words = array_merge(array(
            'groupe',
            'sprl',
            'earl',
            'ei',
            'eirl',
            'eurl',
            'gaec',
            'geie',
            'gie',
            'sarl',
            'sa',
            'sas',
            'sasu',
            'sc',
            'sca',
            'sci',
            'scic',
            'scm',
            'scop',
            'scp',
            'scs',
            'sel',
            'selafa',
            'selarl',
            'selas',
            'selca',
            'sem',
            'seml',
            'sep',
            'sica',
            'snc',
            'l',
            'la',
            'le',
            'les',
            'de',
            'du',
            'des',
            'et',
        ), $stop_words);

        // stop-words - Italy
        $stop_words = array_merge(array(
            'spa',
            'srl',
        ), $stop_words);

        // stop-words - Germany
        $stop_words = array_merge(array(
            'gmbh',
            'gbr',
            'ag',
            'ug',
            'co',
            'kg',
            'ohg',
            'und',
        ), $stop_words);

        // stop-words - Brazil
        $stop_words = array_merge(array(
            'ltda',
            'lda',
            'pllc',
        ), $stop_words);

        // stop-words - North
        $stop_words = array_merge(array(
            'bv', // https://en.wikipedia.org/wiki/Private_limited_company
            'bvba',
            'aps',
            'ab',
            'oy',
        ), $stop_words);

        // custom words
        $custom_stop_words = array_map('strtolower', $custom_stop_words);
        $stop_words = array_merge($custom_stop_words, $stop_words);

        $original_name = trim($company_name);
        $company_name = $original_name;

        // Normalize extended western characters to their ASCII representation
        $company_name = \URLify::transliterate($company_name);

        // Fix in case acronym contains spaces
        // i.e: "B M W Group" => https://www.debuggex.com/r/sBoFrBQBrXJX0y16
        $company_name = preg_replace('/([\s])(?=[a-zA-Z0-9][\s][a-zA-Z0-9]*)/', '', $company_name);

        // Remove all punctuation and control chars
        $company_name = preg_replace('/\p{P}|[\x00-\x08\x0A-\x1F\x7F]|\+/', '', $company_name);

        // If CamelCase, try to split the name by uppercase
        $parts = preg_split('/((?<=[a-z])(?=[A-Z][^\s])|(?=[A-Z\s][a-z])|(?<=[\s])|(\s+))/', $company_name);
        $company_name = implode(' ', $parts);
        $company_name = trim($company_name);

        // Set to lowercase
        $company_name = mb_convert_case($company_name, MB_CASE_LOWER, 'UTF-8');

        // convert all multi-spaces to space
        $company_name = preg_replace('/ +/', ' ', $company_name);

        $company_name = trim($company_name);

        if (!empty($company_name) && 1 < strlen($company_name)) {
            // Split the string into whitespace-separated tokens
            $tokens = explode(' ', $company_name);

            // Remove common stop-words associated with company names
            $new_tokens = array();
            foreach ($tokens as $token) {
                if (!in_array($token, $stop_words, true)) {
                    $new_tokens[] = $token;
                }
            }
            $tokens = $new_tokens;

            // look for an acronym
            $possible_acronym = '';
            $acronym = '';
            $number_of_tokens = count($tokens);
            if ($number_of_tokens > 2) {
                $has_an_acronym = false;
                for ($i=0; $i < $number_of_tokens; $i++) {
                    $token = $tokens[$i];
                    if (strlen($token) === $number_of_tokens - 1) {
                        $token_match_acronym = false;
                        $tokens_without_this_one = $tokens;
                        array_splice($tokens_without_this_one, $i, 1);
                        $j = 0;
                        foreach ($tokens_without_this_one as $t) {
                            if ($t[0] !== $token[$j]) {
                                $token_match_acronym = false;
                                break 1;
                            }

                            $token_match_acronym = true;
                            $j++;
                        }

                        if ($token_match_acronym) {
                            $tokens = $tokens_without_this_one;
                            $acronym = $token;
                            $has_an_acronym = true;
                            break;
                        }
                    }
                }

                if (!$has_an_acronym) {
                    foreach ($tokens as $token) {
                        $possible_acronym .= $token[0];
                    }
                }
            }

            // Sort the tokens and remove duplicates
            sort($tokens);
            $tokens = array_unique($tokens);

            // Join the tokens back together
            $company_name = implode(' ', $tokens);

            // Use a stemming algorithm like Porter's Stemming to stem the company name tokens
            // TODO
        }
        else {
            $tokens = array();
            $company_name = '';
        }

        $return = array(
            'original_name' => $original_name,
            'split' => $tokens,
            'clean_name' => $company_name,
            'clean_name_no_space' => str_replace(' ', '', $company_name),
        );

        if (!empty($acronym)) {
            $return['acronym'] = $acronym;
        }
        elseif (!empty($possible_acronym)) {
            $return['possible_acronym'] = $possible_acronym;
        }

        return $return;
    }
}

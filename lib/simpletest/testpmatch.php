<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains tests that tests the interpretation of a pmatch string.
 *
 * @package pmatch
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/pmatchlib.php');

class pmatch_test extends UnitTestCase {
    protected function match($expect, $string, $expression, $options = null) {
        $string = new pmatch_parsed_string($string, $options);
        $expression = new pmatch_expression($expression, $options);
        $this->assertTrue($expression->is_valid());
        if ($expression->is_valid()){
            $this->assertTrue($expect === $expression->matches($string));
        }
    }

    protected function check_error_message_matches($expression, $errorcode, $errorparam, $options = null){
        $expression = new pmatch_expression($expression, $options);
        $this->assertFalse($expression->is_valid());
        $this->assertEqual(get_string($errorcode, 'pmatch', $errorparam), $expression->get_parse_error());
    }

    public function test_pmatch_error() {
        $this->check_error_message_matches('match_mow([tom maud]|[sid jane]', 'ie_missingclosingbracket', 'match_mow([tom maud]|[sid jane]'); // No closing bracket.
        $this->check_error_message_matches('match_mow()', 'ie_unrecognisedsubcontents', 'match_mow()'); // No contents.
        $this->check_error_message_matches('match_mow([tom maud]|)', 'ie_lastsubcontenttypeorcharacter', '[tom maud]|'); // ends in an or character.
        $this->check_error_message_matches('match_mow([tom maud] )', 'ie_lastsubcontenttypeworddelimiter', 'match_mow([tom maud] )'); // ends in a space.
        $this->check_error_message_matches('match_mow([tom maud]_)', 'ie_lastsubcontenttypeworddelimiter', 'match_mow([tom maud]_)'); // ends in a proximity delimiter.
    }

    public function test_pmatch_matching() {
        // Tests from the original pmatch documentation.
        $this->match(true, 'tom dick harry', 'match(tom dick harry)'); // This is the exact match.
        $this->match(true, 'thomas', 'match_c(tom)'); // Extra characters are allowed anywhere within the word.
        $this->match(true, 'tom dick and harry', 'match_w(dick)'); // Extra words are allowed anywhere within the sentence.
        $this->match(true, 'harry dick tom', 'match_o(tom dick harry)'); // Any order of words is allowed.
        $this->match(true, 'rick', 'match_m(dick)'); // One character in the word can differ.
        $this->match(true, 'rick and harry and tom', 'match_mow(tom dick harry)');
        $this->match(true, 'dick and harry and thomas', 'match_cow(tom dick harry)');
        $this->match(true, 'arthur harry and sid', 'match_mow(tom|dick|harry)'); // Any of tom or dick or harry will be matched.
        $this->match(true, 'tomy harry and sid', 'match_mow(tom|dick harry|sid)'); // The pattern requires either tom or dick AND harry or sid.
        $this->match(true, 'tom was mesmerised by maud', 'match_mow([tom maud]|[sid jane])'); // The pattern requires either (tom and maud) or (sid and jane).
        $this->match(true, 'rick', 'match(?ick)'); // The first character can be anything.
        $this->match(true, 'harold', 'match(har*)'); // Any sequence of characters can follow 'har'.
        $this->match(true, 'tom married maud sid married jane', 'match_mow(tom_maud)'); // Only one word is between tom and maud.
        $this->match(false, 'maud married tom sid married jane', 'match_mow(tom_maud)'); // The proximity control also specifies word order and over-rides the 'o' matching option.
        $this->match(false, 'tom married maud sid married jane', 'match_mow(tom_jane)'); // Only two words are allowed between tom and jane.

        $this->match(true, 'married', 'match_mow(marr*)');
        $this->match(true, 'tom married maud', 'match_mow(tom|thomas marr* maud)');
        $this->match(true, 'maud marries thomas', 'match_mow(tom|thomas marr* maud)');
        $this->match(true, 'tom is to marry maud', 'match_w(tom|thomas marr* maud)');
        $this->match(false, 'tom is to marry maud', 'match_o(tom|thomas marr* maud)');
        $this->match(true, 'tom is to maud marry', 'match_ow(tom|thomas marr* maud)');
        $this->match(false, 'tom is to maud marry', 'match_w(tom|thomas marr* maud)');
        $this->match(true, 'tempratur', 'match_m2ow(temperature)'); // Two characters are missing.
        $this->match(false, 'tempratur', 'match_mow(temperature)'); // Two characters are missing.
        $this->match(true, 'temporatur', 'match_m2ow(temperature)'); // Two characters are incorrect; one has been replaced and one is missing.
        $this->match(false, 'temporatur', 'match_mow(temperature)'); // Two characters are incorrect; one has been replaced and one is missing.
        $this->match(false, 'tmporatur', 'match_m2ow(temperature)'); // Three characters are incorrect; one has been replaced and two are missing.
        
        $this->match(true, 'cat toad frog', 'match(cat [toad|newt frog]|dog)');
        $this->match(true, 'cat newt frog', 'match(cat [toad|newt frog]|dog)');
        $this->match(true, 'cat dog', 'match(cat [toad|newt frog]|dog)');
        $this->match(true, 'dog', 'match([toad frog]|dog)');
        $this->match(true, 'cat toad frog', 'match(cat_[toad|newt frog]|dog)');
        $this->match(true, 'cat newt frog', 'match(cat_[toad|newt frog]|dog)');
        $this->match(true, 'cat dog', 'match(cat_[toad|newt frog]|dog)');
        $this->match(true, 'cat dog', 'match(cat_[toad|newt frog]|dog)');
        $this->match(true, 'x cat x x toad frog x', 'match_w(cat_[toad|newt frog]|dog)');
        $this->match(true, 'x cat newt x x x x x frog x', 'match_w(cat_[toad|newt frog]|dog)');
        $this->match(true, 'x cat x x dog x', 'match_w(cat_[toad|newt frog]|dog)');
        $this->match(false, 'A C B D', 'match([A B]_[C D])');
        $this->match(false, 'B C A D', 'match_o([A B]_[C D])');
        $this->match(true, 'A x x x x B C D', 'match_ow([A B]_[C D])');
        $this->match(false, 'B x x x x A C D', 'match_ow([A B]_[C D])');  //_ requires the words in [] to match in order.
        $this->match(false, 'A B C', 'match_ow([A B]_[B C])');
        $this->match(false, 'A A', 'match(A)');
        
        // Tests of the misspelling rules.
        $this->match(true, 'test', 'match(test)');
        $this->match(false, 'tes', 'match(test)');
        $this->match(false, 'testt', 'match(test)');
        $this->match(false, 'tent', 'match(test)');
        $this->match(false, 'tets', 'match(test)');
 
        $this->match(true, 'test', 'match_mf(test)');
        $this->match(true, 'tes', 'match_mf(test)');
        $this->match(false, 'testt', 'match_mf(test)');
        $this->match(false, 'tent', 'match_mf(test)');
        $this->match(false, 'tets', 'match_mf(test)');
        $this->match(true, 'te', 'match_mf(tes)');
 
        //allow fewer characters
        $this->match(true, 'abcd', 'match_mf(abcd)');
        $this->match(true, 'abc', 'match_mf(abcd)');
        $this->match(false, 'acbd', 'match_mf(abcd)');
        $this->match(false, 'abfd', 'match_mf(abcd)');
        $this->match(false, 'abcf', 'match_mf(abcd)');
        $this->match(true, 'bcd', 'match_mf(abcd)');
        $this->match(false, 'abcdg', 'match_mf(abcd)');
        $this->match(false, 'gabcd', 'match_mf(abcd)');
        $this->match(false, 'abcdg', 'match_mf(abcd)');

        //allow replace character
        $this->match(true, 'abcd', 'match_mr(abcd)');
        $this->match(false, 'abc', 'match_mr(abcd)');
        $this->match(false, 'acbd', 'match_mr(abcd)');
        $this->match(true, 'abfd', 'match_mr(abcd)');
        $this->match(true, 'abcf', 'match_mr(abcd)');
        $this->match(true, 'fbcd', 'match_mr(abcd)');
        $this->match(false, 'bcd', 'match_mr(abcd)');
        $this->match(false, 'abcdg', 'match_mr(abcd)');
        $this->match(false, 'gabcd', 'match_mr(abcd)');
        $this->match(false, 'abcdg', 'match_mr(abcd)');

        //allow transpose characters
        $this->match(true, 'abcd', 'match_mt(abcd)');
        $this->match(false, 'abc', 'match_mt(abcd)');
        $this->match(true, 'acbd', 'match_mt(abcd)');
        $this->match(true, 'bacd', 'match_mt(abcd)');
        $this->match(true, 'abdc', 'match_mt(abcd)');
        $this->match(false, 'abfd', 'match_mt(abcd)');
        $this->match(false, 'abcf', 'match_mt(abcd)');
        $this->match(false, 'fbcd', 'match_mt(abcd)');
        $this->match(false, 'bcd', 'match_mt(abcd)');
        $this->match(false, 'abcdg', 'match_mt(abcd)');
        $this->match(false, 'gabcd', 'match_mt(abcd)');
        $this->match(false, 'abcdg', 'match_mt(abcd)');


        //allow extra character
        $this->match(true, 'abcd', 'match_mx(abcd)');
        $this->match(false, 'abc', 'match_mx(abcd)');
        $this->match(false, 'acbd', 'match_mx(abcd)');
        $this->match(false, 'bacd', 'match_mx(abcd)');
        $this->match(false, 'abdc', 'match_mx(abcd)');
        $this->match(false, 'abfd', 'match_mx(abcd)');
        $this->match(false, 'abcf', 'match_mx(abcd)');
        $this->match(false, 'fbcd', 'match_mx(abcd)');
        $this->match(false, 'bcd', 'match_mx(abcd)');
        $this->match(true, 'abcdg', 'match_mx(abcd)');
        $this->match(true, 'gabcd', 'match_mx(abcd)');
        $this->match(true, 'abcdg', 'match_mx(abcd)');
        $this->match(true, 'abcd', 'match_mx(abcd)');
        $this->match(false, 'abc', 'match_mx(abcd)');
        

        //allow any one mispelling
        $this->match(true, 'abcd', 'match_m(abcd)');
        $this->match(true, 'abc', 'match_m(abcd)');
        $this->match(true, 'acbd', 'match_m(abcd)');
        $this->match(true, 'bacd', 'match_m(abcd)');
        $this->match(true, 'abdc', 'match_m(abcd)');
        $this->match(true, 'abfd', 'match_m(abcd)');
        $this->match(true, 'abcf', 'match_m(abcd)');
        $this->match(true, 'fbcd', 'match_m(abcd)');
        $this->match(true, 'bcd', 'match_m(abcd)');
        $this->match(true, 'abcdg', 'match_m(abcd)');
        $this->match(true, 'gabcd', 'match_m(abcd)');
        $this->match(true, 'abcdg', 'match_m(abcd)');

        $this->match(false, 'bacde', 'match_m(abcd)');
        $this->match(false, 'badc', 'match_m(abcd)');
        $this->match(false, 'affd', 'match_m(abcd)');
        $this->match(false, 'fbcf', 'match_m(abcd)');
        $this->match(false, 'ffcd', 'match_m(abcd)');
        $this->match(false, 'bfcd', 'match_m(abcd)');
        $this->match(false, 'abccdg', 'match_m(abcd)');
        $this->match(false, 'gabbcd', 'match_m(abcd)');
        $this->match(false, 'abbcdg', 'match_m(abcd)');

        //allow any two mispelling
        $this->match(true, 'abcd', 'match_m2(abcd)');
        $this->match(true, 'abc', 'match_m2(abcd)');
        $this->match(true, 'acbd', 'match_m2(abcd)');
        $this->match(true, 'bacd', 'match_m2(abcd)');
        $this->match(true, 'abdc', 'match_m2(abcd)');
        $this->match(true, 'abfd', 'match_m2(abcd)');
        $this->match(true, 'abcf', 'match_m2(abcd)');
        $this->match(true, 'fbcd', 'match_m2(abcd)');
        $this->match(true, 'bcd', 'match_m2(abcd)');
        $this->match(true, 'abcdg', 'match_m2(abcd)');
        $this->match(true, 'gabcd', 'match_m2(abcd)');
        $this->match(true, 'abcdg', 'match_m2(abcd)');

        $this->match(true, 'bacde', 'match_m2(abcd)');
        $this->match(true, 'badc', 'match_m2(abcd)');
        $this->match(true, 'affd', 'match_m2(abcd)');
        $this->match(true, 'fbcf', 'match_m2(abcd)');
        $this->match(true, 'ffcd', 'match_m2(abcd)');
        $this->match(true, 'bfcd', 'match_m2(abcd)');
        $this->match(true, 'abccdg', 'match_m2(abcd)');
        $this->match(true, 'gabbcd', 'match_m2(abcd)');
        $this->match(true, 'abbcdg', 'match_m2(abcd)');

    }


}
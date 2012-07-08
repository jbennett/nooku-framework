/**
 * @version     $Id$
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
var ComUsers = {

    Password:{

        score:function (password, words) {

            // Check if zxcvbn.js is already loaded.
            if (typeof zxcvbn !== 'function') return 0;

            var result = zxcvbn(password, words);

            return result.score;
        },

        checker:function (config) {

            var my = {};

            my.score = 0;
            my.config = config;

            my.initialize = function () {

                var config = this.config;

                window.addEvent('domready', function () {
                    $(config.input_id).addEvent('keyup', function () {

                        // Get user input values.
                        var words = Array.copy(config.words);
                        Array.each(config.user_input_ids, function (user_input_id, index) {
                            words.push($(user_input_id).get('value'));
                        });

                        var password = this.get('value');

                        my.score = ComUsers.Password.score(password, words);

                        if (password.length) my.score += 1;

                        $(config.container_id).set('class', config['class'] + ' ' + 'score' + my.score);
                        $(config.container_id).set('html', config.score_map[my.score]);
                    });

                    // Update password score on user input change.
                    Array.each(config.user_input_ids, function (user_input_id, index) {

                        var fireEvent = function () {
                            $(config.input_id).fireEvent('keyup');
                        };

                        $(user_input_id).addEvents({"keyup":fireEvent, "change":fireEvent});
                    });

                    // Intercept the move request in the controller chain.
                    if (config.min_score) {
                        $(config.container_id).getParent('form').addEvent('submit', function () {
                            if (my.score < config.min_score) {
                                alert(config.min_score_msg);
                                return false;
                            }
                        });
                    }

                    // Add mininum length constraint (if any).
                    if (config.min_len) {
                        $(config.input_id).addEvent('change', my.lenCheck);
                        $(config.container_id).getParent('form').addEvent('submit', my.lenCheck);
                        var koowa_form = $(config.container_id).getParent('.-koowa-form');
                        if (koowa_form) {
                            koowa_form.addEvent('before.save', function () {
                                return my.lenCheck();
                            });
                            koowa_form.addEvent('before.apply', function () {
                                return my.lenCheck();
                            });
                        }
                    }
                });
            };

            my.lenCheck = function () {
                var config = my.config;
                var password = $(config.input_id).get('value');
                if (password.length && password.length < config.min_len) {
                    alert(config.min_len_msg);
                    return false;
                }
            }

            my.initialize();

            return my;
        }
    }
}
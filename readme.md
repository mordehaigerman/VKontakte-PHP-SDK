VKontakte PHP SDK
=================

The VK Platform http://vk.com/developers.php is a set of APIs that make your
application more social.

Usage
-----

To create a new instance of VKontakte:

    <?php
        /** @see VKontakte */
        require_once 'vkontakte.php';

        // the constructor uses VKontakte::setOptions($_GET)
        $vkontakte = new VKontakte();

To make the API calls:

        try {
            $profiles = $vkontakte->api('getPrifiles', array(
                'uids' => $vkontakte->getViewerId()
            ));
        } catch (VKontakteApiException $e) {
            @error_log($e);
        }
    ?>

Feedback
--------

Use GitHub issues tracker to report bugs and issues
https://github.com/mordehaigerman/VKontakte-PHP-SDK/issues.

License
-------

The VKontakte PHP SDK is released under the New BSD License
https://github.com/mordehaigerman/VKontakte-PHP-SDK/blob/master/license.txt.
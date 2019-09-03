<?php
/**
 * @file
 * Contains \Drupal\doc_page\Controller\DocController.
 */

namespace Drupal\doc_page\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Link;
use Drupal\Component\Utility\Html;

class DocController {
    public function content() {

        $error = array(
            '#markup' => 'No documentation found. Please contact an administrator.',
        );

        // Get the current user
        $user = \Drupal::currentUser();

        // Check for permission
        if ($user->hasPermission('administer doc page')) {
        $admin_link = Link::createFromRoute('Administer documentation page', 'doc.settings', []);
            $error['#markup'] = 'No URL is specified or the URL is bad.<br><br>' . $admin_link->toString();
        }

        //Get values
        $config = \Drupal::config('doc.adminsettings');
        $url = $config->get('doc_url');
        $valid = UrlHelper::isValid($url, $absolute = TRUE);

        if (!isset($url) || $url === '' || $valid == FALSE) {
            return $error;
        }

        // Get content.
        $path = $url;
        $html = file_get_contents($path);
        if (!isset($html) || $html == '') {
            return $error;
        }

        // Remove script tag messing stuff up.
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        // Remove code{} part at the begging of the document.
        $html = preg_replace('#code{(.*?)}#is', '', $html);

        $doc = Html::load($html);
        $imageTags = $doc->getElementsByTagName('img');
        // Get images from source site.
        foreach($imageTags as $tag) {
            $src = $tag->getAttribute('src');
            $tag->setAttribute('src', $path.$src);
        }

        // Return html.
        return array(
            '#type' => 'markup',
            '#markup' =>  '<div class="documentation-wrap">' . Html::serialize($doc) . '</div>',
        );
    }
}

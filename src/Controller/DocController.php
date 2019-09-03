<?php
/**
 * @file
 * Contains \Drupal\doc_page\Controller\DocController.
 */

namespace Drupal\doc_page\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Link;
use Drupal\Component\Utility\Html;
use Michelf\MarkdownExtra;

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
        $markdown = $config->get('doc_markdown');
        $valid = UrlHelper::isValid($url, $absolute = TRUE);
        if (!isset($url) || !isset($markdown)) {
            return $error;
        }

        if (!isset($markdown)) {

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

        if (isset($markdown[0])) {
            #include_once \Drupal::root() . '/modules/custom/doc_page/parsedown/Parsedown.php';
            #include_once drupal_get_path('module', 'doc_page');
            $markdown_display = '';
            $file = \Drupal::entityTypeManager()->getStorage('file')->load($markdown[0]);
            $text = file_get_contents($file->url());

           $markdown_display = MarkdownExtra::defaultTransform($text);
            #$markdown_display = Parsedown::instance()->text('Hello _Parsedown_!');
            #dpm($parsedown);

            #dpm($markdown_display);
                        // Return html.
                        return array(
                            '#type' => 'markup',
                            '#markup' =>  '<div class="documentation-wrap">' . $markdown_display . '</div>',
                        );
        }
        // Return html.
        return array(
            '#type' => 'markup',
            '#markup' =>  '<div class="documentation-wrap">Oh boy</div>',
        );
    }
}

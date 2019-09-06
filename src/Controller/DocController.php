<?php
/**
 * @file
 * Contains \Drupal\doc_page\Controller\DocController.
 */

namespace Drupal\doc_page\Controller;

use Drupal\Core\Link;
use Drupal\Component\Utility\Html;
use Drupal\ghmarkdown\cebe\markdown\MarkdownExtra;

class DocController {
  public function content() {
    $path = '';
    $error = array(
      '#markup' => 'No documentation found. Please contact an administrator.',
    );

    // Get the current user
    $user = \Drupal::currentUser();

    // Add a link to settings form if user has permission.
    if ($user->hasPermission('administer doc page')) {
      $admin_link = Link::createFromRoute('Administer documentation page', 'doc.settings', []);
      $error['admin_link']['#markup'] = '<br><br>' . $admin_link->toString();
    }

    //Get values
    $config = \Drupal::config('doc.adminsettings');
    $url = $config->get('doc_url');

    // If link to markdown file.
    if (isset($url) && $url != '') {
      // Get content.
      $file_headers = @get_headers($url);
      if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
        $error['#markup'] = 'File was not found';
        return $error;
      } else {
        $html = file_get_contents($url);
      }
      if (!isset($html) || $html == '') {
        return $error;
      }
    } else {
      return $error;
    }

    // Create html from the markdown.
    $markdown = new MarkdownExtra();
    $markdown_display = $markdown->parse($html);

    // Create a DOM object from the html.
    $doc = Html::load($markdown_display);
    $imageTags = $doc->getElementsByTagName('img');

    $url_data = parse_url($url);
    $root = $url_data['scheme']. '://' .$url_data['host'] .'/';

    //Make root path for images.
    $image_path_array = $url_data['path'];
    $image_path_array = explode('/', $image_path_array);
    array_pop($image_path_array);
    $image_path = $root.implode('/', $image_path_array).'/';

    // Rewrite image paths.
    foreach($imageTags as $tag) {
      $src = $tag->getAttribute('src');
      $tag->setAttribute('src', $image_path . $src);
    }

    // Create html from the DOM object.
    $markdown_display = Html::serialize($doc);
    $markdown_display = Html::decodeEntities($markdown_display);

    // Return html.
    return array(
      '#type' => 'markup',
      '#markup' =>  '<div class="documentation-wrap">' . $markdown_display . '</div>',
    );
  }

  public function doc_build_url(array $parts) {
    return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
      ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
      (isset($parts['user']) ? "{$parts['user']}" : '') .
      (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
      (isset($parts['user']) ? '@' : '') .
      (isset($parts['host']) ? "{$parts['host']}" : '') .
      (isset($parts['port']) ? ":{$parts['port']}" : '') .
      (isset($parts['path']) ? "{$parts['path']}" : '') .
      (isset($parts['query']) ? "?{$parts['query']}" : '') .
      (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
  }
}

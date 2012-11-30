<?php

/**
 * @file
 * Default theme implementation to display a profile.
 *
 * Available variables:
 * - $title: The (sanitized) profile type label.
 * - $content: An array of profile items. Use render($content) to print them
 *   all, or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $url: Direct URL of the current profile.
 * - $page: TRUE if this is the main view page $url points too.
 * - $attributes: An instance of Attributes class that can be manipulated as an
 *    array and printed as a string.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $profile: Full profile entity. Contains data that may not be safe.
 *
 * @see template_preprocess()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<article class="<?php print $attributes['class']; ?> clearfix"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>><a href="<?php print $url; ?>" rel="bookmark"><?php print $title; ?></a></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <div class="content"<?php print $content_attributes; ?>>
    <?php
      print render($content);
    ?>
  </div>

</article>

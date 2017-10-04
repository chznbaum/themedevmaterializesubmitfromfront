<?php
/*
Plugin Name: TDM Submit From Front
Plugin URI:
Description: This creates a form so that posts can be submitted from the front end
Version: 1.0
Author: Chazona Baum
Author URI: http://chazonabaum.com
*/

class ThemedevmaterializeFrontEndSubmit {

  protected $pluginPath;
  protected $pluginUrl;

  public function __construct() {
    // Set Plugin Path
    $this->pluginPath = dirname(__FILE__);
    // Set PluginUrl
    $this->pluginUrl = WP_PLUGIN_URL . '/themedevmaterializesubmitfromfront';
    // Add the shortcode
    add_shortcode('TDM_FRONT_END_SUBMIT', array($this, 'themedevmaterializeHandleFrontEndForm'));
  }

  function themedevmaterializeHandleFrontEndForm() {
    // Check if the user has permission to publish the post.
    if ( !current_user_can('publish_posts') ) {
      echo '<p class="flow-text red-text">Please log in as a user authorized to post.</p>';
      return;
    }

    if( $this->themedevmaterializeIsFormSubmitted() && $this->themedevmaterializeIsNonceSet() ) {
      if( $this->themedevmaterializeIsFormValid() ) {
        $this->themedevmaterializecreatePost();
      } else {
        $this->themedevmaterializeDisplayForm();
      }
    } else {
      $this->themedevmaterializeDisplayForm();
    }

  }

  function themedevmaterializeIsFormSubmitted() {

    if( isset( $_POST['submitForm'] ) ) return true;
    else return false;

  }

  function themedevmaterializeIsNonceSet() {

    if( isset( $_POST['nonce_field_for_front_end_new_post'] ) && wp_verify_nonce( $_POST['nonce_field_for_front_end_new_post'], 'front_end_new_post' ) )
    return true;
    else return false;

  }

  function themedevmaterializeIsFormValid() {

    // Check that all mandatory fields are present.
    if( trim( $_POST['postTitle'] ) === '' ) {
      $error = 'Please enter a title.';
      $hasError = true;
    } else if( trim( $_POST['themedevmaterialize_post_content'] ) === '' ) {
      $error = 'Please enter the content. Content: ' . $_POST['themedevmaterialize_post_content'];
      $hasError = true;
    }

    // Check if any error was detected in validation.
    if( $hasError == true ) {
      echo '<p class="flow-text red-text">' . $error . '</p>';
      return false;
    }
    return true;

  }

  function themedevmaterializecreatePost() {

    // Get the ID of currently logged in user to set as post author
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;

    // Get the details from the form which was posted
    $postTitle = $_POST['postTitle'];
    $contentOfPost = $_POST['themedevmaterialize_post_content'];
    $postExcerpt = $_POST['postExcerpt'];
    $postStatus = 'pending'; // Manually approve all posts

    // Create the post in WordPress
    $post_id = wp_insert_post( array(
      'post_title' => $postTitle,
      'post_content' => $contentOfPost,
      'post_excerpt' => $postExcerpt,
      'post_status' => $postStatus,
      'post_author' => $currentuserid
    ) );

    echo '<p class="flow-text green-text">Thank you for submitting your post! It will be reviewed within the next 48 hours.</p>';

  }

  public function themedevmaterializeDisplayForm() {
  ?>
  <div id="front_end_publisher">
    <div class="row">
      <form action="" id="frontEndPostForm" method="POST" enctype="multipart/form-data">
        <div class="col s12 m8">
          <div class="row">
            <p class="grey-text">Please fill out the following to submit your post. All guest posts are subject to approval.</p>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <label for="postTitle">Title</label>
              <input type="text" name="postTitle" id="postTitle" />
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <?php wp_editor( '', 'themedevmaterialize_post_content', array( 'drag_drop_upload' => true, 'editor_height' => '300px' ) ); ?>
            </div>
          </div>
        </div>
        <div class="col s12 m4">
          <div class="row">
            <?php wp_nonce_field( 'front_end_new_post', 'nonce_field_for_front_end_new_post' ); ?>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <textarea name="postExcerpt" id="postExcerpt" class="materialize-textarea"></textarea>
              <label for="postExcerpt">Excerpt</label>
            </div>
          </div>
          <div class="row">
            <p>
              <input type="checkbox" class="filled-in" id="postRightToUse" required>
              <label for="postRightToUse">I have the right to post this content and any photos I have included.</label>
            </p>
          </div>
          <div class="row">
            <button class="btn waves-effect waves-light" type="submit" name="submitForm">Create Post</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php
}

}

$themedevmaterializeSubmitFromFEObj = new ThemedevmaterializeFrontEndSubmit();

?>
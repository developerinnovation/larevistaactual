<?php

namespace Drupal\save_node\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * 
 */
class SaveNodeController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function Render($nid) {
    $node = \Drupal::entityManager()->getStorage('node')->load($nid);
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $build = array(
      '#type' => 'markup',
      '#markup' =>  $user->get('mail')->value,
    );
    // This is the important part, because will render only the TWIG template.
    $response = new AjaxResponse();


   // return new Response($build);
    //print "<pre>".print_r($user,1).print"</pre>";
  }

}

class MyModuleController extends ControllerBase {
  // ...
// AJAX Callback to read a message.
  public function readMessageCallback($method, $mid) {
    $message = mymodule_load_message($mid);
    // Create AJAX Response object.
    $response = new AjaxResponse();
    // Call the readMessage javascript function.
    $response->addCommand( new ReadMessageCommand($message));
   
    // Return ajax response.
    return $response;
  }
  // ...
}
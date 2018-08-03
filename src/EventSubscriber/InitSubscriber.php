<?php /**
 * @file
 * Contains \Drupal\answers\EventSubscriber\InitSubscriber.
 */

namespace Drupal\answers\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    if (arg(0) == 'node' && is_numeric(arg(1)) && arg(2) == '') {
      $node = node_load(arg(1));
      if ($node != '') {
        $node = entity_metadata_wrapper('node', $node);
        if ($node->type->value() == 'answers_answer') {
          drupal_goto('node/' . $node->answers_related_question->value()->nid, [
            'fragment' => 'node-' . $node->nid->value(),
            'alias' => TRUE,
          ]);
        }
      }
    }
  }

}
